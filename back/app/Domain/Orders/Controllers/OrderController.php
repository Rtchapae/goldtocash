<?php

namespace App\Domain\Orders\Controllers;

use App\Domain\Orders\Actions\CreateKitRequestAction;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Repositories\OrderRepositoryInterface;
use App\Domain\Orders\Requests\RegisterKitRequest;
use App\Domain\Orders\Resources\KitRegistrationResource;
use App\Domain\Orders\Resources\PrintLabelResource;
use App\Domain\Orders\Services\KitRegistrationService;
use App\Domain\Orders\Services\OrderLabelService;
use App\Domain\Orders\Services\OrderStatusUpdateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function __construct(
        private readonly KitRegistrationService $kitRegistrationService,
        private readonly OrderLabelService $orderLabelService,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly CreateKitRequestAction $createKitRequestAction,
        private readonly OrderStatusUpdateService $orderStatusUpdateService,
    ) {
    }

    public function registerKit(RegisterKitRequest $request): JsonResponse
    {
        try {
            $result = $this->kitRegistrationService->registerKit($request->validated());

            return response()->json(new KitRegistrationResource($result));
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'error' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Kit registration error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $errorMessage = $e->getMessage();
            $userFriendlyMessage = 'An error occurred during registration. Please try again.';

            if (str_contains($errorMessage, 'User address is incomplete')) {
                $userFriendlyMessage = 'Your account address is incomplete. Please update your profile information before requesting a kit.';
            }

            elseif (str_contains($errorMessage, 'Invalid address')) {
                $userFriendlyMessage = 'Your address could not be validated. Please check your address information and try again.';
            }

            elseif (str_contains($errorMessage, 'Failed to send verification code')) {
                $userFriendlyMessage = 'We could not send a verification code to your phone number. Please check that your phone number is correct and try again.';
            }

            elseif (str_contains($errorMessage, 'At least 1 phone was invalid')) {
                $userFriendlyMessage = 'The phone number format appears to be invalid. Please check your phone number and try again.';
            }

            return response()->json([
                'status' => false,
                'error' => $userFriendlyMessage,
            ], 400);
        }
    }

    public function getPrintLabel(int $orderId): \Illuminate\Http\Response
    {
        try {
            $user = Auth::guard('front')->user();

            if ($user) {
                $result = $this->orderLabelService->getPrintLabelUrl($orderId, $user);
            } else {
                $result = $this->orderLabelService->getPrintLabelUrlPublic($orderId);
            }

            if (!$result['status']) {
                abort(404, $result['error'] ?? 'Label not found');
            }

            $labelFilePath = storage_path("app/docs/clients/{$result['order']->user_id}/{$orderId}/shipping-label.png");

            if (!file_exists($labelFilePath)) {
                $regenerated = $this->tryRegenerateLabelFile($result['order'], $labelFilePath);

                if (!$regenerated) {
                    $orderDir = dirname($labelFilePath);
                    $pngFiles = glob($orderDir . '/*.png');

                    if (!empty($pngFiles)) {
                        $labelFilePath = $pngFiles[0];
                    } else {
                        abort(404, 'Shipping label is not available yet. Please contact support if you believe this is an error.');
                    }
                }
            }

            $fileBody = file_get_contents($labelFilePath);
            $mimeType = mime_content_type($labelFilePath) ?: 'image/png';

            return response($fileBody, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="shipping-label-' . $orderId . '.png"',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get print label: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'trace' => $e->getTraceAsString(),
            ]);

            abort(500, 'An error occurred while retrieving the label.');
        }
    }

    public function confirmAccountCreation(int $orderId): JsonResponse
    {
        try {
            $order = $this->orderRepository->findById($orderId);

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'error' => 'Order not found',
                ], 404);
            }

            $order->update([
                'account_confirmed' => true,
                'account_confirmed_at' => now(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Account creation confirmed',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to confirm account creation: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'error' => 'An error occurred while confirming account creation.',
            ], 500);
        }
    }

    public function getTestFile(): \Illuminate\Http\Response
    {
        $pngData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');

        return response($pngData, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'inline; filename="test.png"'
        ]);
    }

    private function tryRegenerateLabelFile($order, string $targetPath): bool
    {
        try {
            if (empty($order->description)) {
                return false;
            }

            $fedexData = json_decode($order->description, true);

            if (!$fedexData || !isset($fedexData['output']['transactionShipments'][0]['pieceResponses'][0]['packageDocuments'][0]['encodedLabel'])) {
                return false;
            }

            $encodedLabel = $fedexData['output']['transactionShipments'][0]['pieceResponses'][0]['packageDocuments'][0]['encodedLabel'];

            $imageService = new \App\Domain\Orders\Services\ImageService();
            $processedLabel = $imageService->cropImageFromBase64($encodedLabel);

            $dir = dirname($targetPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $imageData = base64_decode($processedLabel);
            file_put_contents($targetPath, $imageData);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to regenerate label file', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    public function getUserOrders(): JsonResponse
    {
        try {
            $user = Auth::guard('front')->user();

            if (!$user) {
                abort(401, 'Unauthorized');
            }

            $orders = $this->orderRepository->getUserOrders($user->id);

            return response()->json([
                'status' => true,
                'orders' => $orders,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get user orders: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'error' => 'An error occurred while retrieving orders.',
            ], 500);
        }
    }

    public function createKitRequest(Request $request): JsonResponse
    {
        try {
            $attribution = $request->validate([
                'submission_url' => ['nullable', 'string', 'max:2048'],
                'referrer' => ['nullable', 'string', 'max:2048'],
                'utm_source' => ['nullable', 'string', 'max:255'],
                'utm_medium' => ['nullable', 'string', 'max:255'],
                'utm_campaign' => ['nullable', 'string', 'max:255'],
                'utm_term' => ['nullable', 'string', 'max:255'],
                'utm_content' => ['nullable', 'string', 'max:255'],
            ]);

            $result = $this->createKitRequestAction->execute($attribution);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Failed to create kit request: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'error' => 'An error occurred while creating kit request. Please try again.',
            ], 500);
        }
    }

    public function viewLetter(int $orderId): \Illuminate\Http\Response
    {
        $user = Auth::guard('front')->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $order = $this->orderRepository->findByIdAndUserId($orderId, $user->id);

        if (!$order) {
            abort(404, 'Order not found');
        }

        $filePath = storage_path("app/docs/clients/{$user->id}/{$order->id}/letter.pdf");

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        $fileBody = file_get_contents($filePath);
        $mimeType = mime_content_type($filePath) ?: 'application/pdf';

        return response($fileBody, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="order-' . $order->id . '-letter.pdf"',
        ]);
    }

    public function respondToOffer(Request $request, int $orderId): JsonResponse
    {
        $user = Auth::guard('front')->user();

        if (!$user) {
            return response()->json(['status' => false, 'error' => 'Unauthorized'], 401);
        }

        $order = $this->orderRepository->findByIdAndUserId($orderId, $user->id);

        if (!$order) {
            return response()->json(['status' => false, 'error' => 'Order not found'], 404);
        }

        if ((int) $order->status !== OrderStatus::OFFER_SENT->value) {
            return response()->json([
                'status' => false,
                'error' => 'This order is not pending an offer response.',
            ], 422);
        }

        $action = $request->input('action');
        if (!in_array($action, ['accept', 'deny'], true)) {
            return response()->json([
                'status' => false,
                'error' => 'Invalid action. Use "accept" or "deny".',
            ], 422);
        }

        $newStatus = $action === 'accept' ? OrderStatus::OFFER_ACCEPTED : OrderStatus::OFFER_DENIED;
        $this->orderStatusUpdateService->updateOrderStatus($order, $newStatus, 'Client responded to offer');

        return response()->json([
            'status' => true,
            'message' => $action === 'accept' ? 'Offer accepted.' : 'Offer declined.',
            'order' => [
                'id' => $order->id,
                'status' => $newStatus->value,
            ],
        ]);
    }
}

