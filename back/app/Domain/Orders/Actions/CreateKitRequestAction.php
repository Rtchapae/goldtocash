<?php

namespace App\Domain\Orders\Actions;

use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Enums\OrderType;
use App\Domain\Orders\Repositories\OrderRepositoryInterface;
use App\Domain\Orders\Services\FedexService;
use App\Domain\Users\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Exception;

class CreateKitRequestAction
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly FedexService $fedexService,
    ) {
    }

    public function execute(): array
    {
        /** @var User|null $user */
        $user = Auth::guard('front')->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        try {
            if (empty($user->address) || empty($user->city) || empty($user->state) || empty($user->zip)) {
                throw new Exception('Invalid address, phone or email!');
            }

            $userParams = [
                'address' => $user->address,
                'city' => $user->city,
                'state' => $user->state,
                'zip' => $user->zip,
                'phone' => $user->phone,
                'email' => $user->email,
            ];

            if (!$this->fedexService->validateAddress($userParams)) {
                throw new Exception('Invalid address, phone or email!');
            }

            $labelData = $this->fedexService->createShippingLabel($user);
            $track_number = $labelData['track_number'];
            $label = $labelData['label'];
            $fedexOrder = $labelData['fedex_order'];

            $order = $this->orderRepository->create([
                'user_id' => $user->id,
                'status' => OrderStatus::KIT_REQUESTED->value,
                'order_type' => OrderType::ONLINE->value,
                'welcome' => true,
                'send_label' => false,
                'description' => json_encode($fedexOrder),
            ]);

            $this->saveShippingLabel($order, $label);

            $locations = $this->fedexService->getLocationsForZip($user->zip ?? '');

            $logoBase64 = $this->imageToBase64(public_path('images/logo-black-with-gold-c.png'));
            $ratingBase64 = $this->imageToBase64(public_path('images/rating.png'));
            $formBase64 = $this->imageToBase64(public_path('images/form.jpg'));

            $path = storage_path("app/docs/clients/{$user->id}/{$order->id}");
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $pdf = PDF::loadView('emails.welcome-letter', compact('order', 'user', 'track_number', 'locations', 'label', 'logoBase64', 'ratingBase64', 'formBase64'))
                ->setPaper('letter', 'portrait');

            file_put_contents($path . "/letter.pdf", $pdf->output());

            $this->orderRepository->sendKitRequestEmail($user, $order);

            return [
                'status' => true,
                'message' => 'Kit request created successfully',
                'order_id' => $order->id,
                'order' => [
                    'id' => $order->id,
                    'status' => $order->status,
                    'created_at' => $order->created_at->toISOString(),
                ],
            ];
        } catch (Exception $e) {
            Log::warning("Failed to create order", [
                'message' => $e->getMessage(),
                'userId' => $user->id ?? null,
                'code' => $e->getCode(),
            ]);

            if ($e->getCode() == 400 || $e->getCode() == 4000 || $e->getCode() == 4001) {
                throw new Exception('Error! Invalid address, phone or email!');
            }

            throw $e;
        }
    }

    private function imageToBase64(string $imagePath): ?string
    {
        if (!file_exists($imagePath)) {
            Log::warning('Image file not found for PDF', ['path' => $imagePath]);
            return null;
        }

        $imageData = file_get_contents($imagePath);
        $imageInfo = getimagesize($imagePath);
        $mimeType = $imageInfo['mime'] ?? 'image/png';

        return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
    }

    private function saveShippingLabel($order, string $label): void
    {

        try {
            $path = storage_path("app/docs/clients/{$order->user_id}/{$order->id}");

            if (!is_dir($path)) {
                $mkdirResult = mkdir($path, 0777, true);
            }

            $imageData = base64_decode($label);
            $filePath = $path . "/shipping-label.png";

            $bytesWritten = file_put_contents($filePath, $imageData);

        } catch (\Exception $e) {
            Log::error('saveShippingLabel: Exception occurred', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}

