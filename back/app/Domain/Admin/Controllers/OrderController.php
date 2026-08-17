<?php

namespace App\Domain\Admin\Controllers;

use App\Domain\Orders\Actions\ListOrdersAction;
use App\Domain\Orders\Actions\ListPendingOffersAction;
use App\Domain\Orders\Actions\ListPaidOrdersAction;
use App\Domain\Admin\Actions\CreateOrderAction;
use App\Domain\Admin\Actions\UpdateOrderShippingAction;
use App\Domain\Orders\Repositories\OrderRepositoryInterface;
use App\Domain\Admin\Requests\ListOrdersRequest;
use App\Domain\Admin\Requests\CreateOrderRequest;
use App\Domain\Admin\Requests\UpdateOrderRequest;
use App\Domain\Admin\Requests\UpdateOrderShippingRequest;
use App\Http\Controllers\Controller;
use App\Domain\Admin\Resources\OrderResource;
use App\Domain\Admin\Resources\OrderResourceCollection;
use App\Domain\Admin\Actions\UpdateOrderAction;
use App\Domain\Orders\Support\OfflineOrderPdfData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly CreateOrderAction $createOrderAction,
        private readonly ListPendingOffersAction $listPendingOffersAction,
        private readonly ListPaidOrdersAction $listPaidOrdersAction,
        private readonly UpdateOrderShippingAction $updateOrderShippingAction,
        private readonly UpdateOrderAction $updateOrderAction,
    ) {
    }

    public function index(ListOrdersRequest $request, ListOrdersAction $action): JsonResponse
    {
        $paginator = $action->execute(
            perPage: $request->perPage(),
            page: $request->page(),
            nameQuery: $request->nameQuery(),
            sourceFilter: $request->sourceFilter(),
            utmCampaignFilter: $request->utmCampaignFilter(),
            utmMediumFilter: $request->utmMediumFilter(),
            period: $request->period(),
            from: $request->from(),
            to: $request->to(),
            orderBy: $request->orderBy(),
            orderDir: $request->orderDir(),
            orderType: $request->orderType(),
            branchId: $request->branchId(),
            export: $request->input('export', false),
        );

        return response()->json(new OrderResourceCollection(
            OrderResource::collection($paginator->items()),
            $paginator,
            [
                'sources' => $action->getAvailableSources(),
                'utm_campaigns' => $action->getAvailableUtmCampaigns(),
                'utm_mediums' => $action->getAvailableUtmMediums(),
                'branches' => $action->getAvailableBranches(),
            ]
        ));
    }

    public function show(int $id): JsonResponse
    {
        $order = $this->orderRepository->findById($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found',
            ], 404);
        }

        return response()->json(new OrderResource($order));
    }

    public function getFiles(int $id): JsonResponse
    {
        $order = $this->orderRepository->findById($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found',
            ], 404);
        }

        $path = storage_path("app/docs/clients/{$order->user_id}/{$order->id}");
        $files = [];

        if (is_dir($path)) {
            $fileList = scandir($path);
            foreach ($fileList as $file) {
                if ($file !== '.' && $file !== '..' && is_file($path . '/' . $file)) {
                    $filePath = $path . '/' . $file;
                    $fileSize = filesize($filePath);
                    $fileModified = filemtime($filePath);

                    $fileInfo = $this->getFileInfo($file, $order);

                    $files[] = [
                        'id' => $order->id . '-' . $file,
                        'title' => $fileInfo['title'],
                        'type' => $fileInfo['type'],
                        'filename' => $file,
                        'size' => $fileSize,
                        'created_at' => date('Y-m-d H:i:s', $fileModified),
                        'viewUrl' => config('app.frontend_url', config('app.url')) . "/api/v1/front/user/orders/{$order->id}/" . $fileInfo['url_segment'],
                        'apiPath' => "/admin/orders/{$order->id}/files/{$file}",
                    ];
                }
            }
        }

        return response()->json([
            'files' => $files
        ]);
    }

    public function downloadFile(int $id, string $filename)
    {
        $order = $this->orderRepository->findById($id);

        if (!$order) {
            abort(404, 'Order not found');
        }

        $safeFilename = basename($filename);
        $path = storage_path("app/docs/clients/{$order->user_id}/{$order->id}/{$safeFilename}");

        if (!is_file($path)) {
            abort(404, 'File not found');
        }

        return response()->file($path);
    }

    private function getFileInfo(string $filename, $order): array
    {
        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if ($extension === 'pdf') {
            if (str_contains($filename, 'letter') || str_contains($filename, 'offline_order')) {
                return [
                    'title' => $order->order_type === 'offline' ? "Order #{$order->id} Appraisal Kit" : "Order #{$order->id} Welcome Letter",
                    'type' => 'shipping',
                    'url_segment' => 'letter'
                ];
            }
        } elseif (in_array($extension, ['png', 'jpg', 'jpeg'])) {
            if (str_contains($filename, 'shipping-label')) {
                return [
                    'title' => "Order #{$order->id} Shipping Label",
                    'type' => 'shipping',
                    'url_segment' => 'label'
                ];
            }
        }

        return [
            'title' => "Order #{$order->id} " . ucfirst($baseName),
            'type' => 'other',
            'url_segment' => 'file/' . $filename
        ];
    }

    public function generatePdf(int $id)
    {
        $order = $this->orderRepository->findById($id);

        if (!$order) {
            abort(404, 'Order not found');
        }

        if ($order->order_type !== 'offline') {
            abort(404, 'PDF generation is only available for offline orders');
        }

        $order->load(['user', 'branch']);

        $buyerName = Auth::guard('admin')->user()?->name ?? '';

        $pdf = Pdf::loadView('pdf.offline_order', OfflineOrderPdfData::forOrder(
            $order,
            $order->user,
            $buyerName
        ));

        $pdf->setPaper('letter', 'portrait');

        $path = storage_path("app/docs/clients/{$order->user_id}/{$order->id}");
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        file_put_contents($path . "/offline_order.pdf", $pdf->output());

        return $pdf->download('offline_order_' . $order->id . '.pdf');
    }

    public function previewPdfHtml(int $id)
    {
        $order = $this->orderRepository->findById($id);

        if (!$order) {
            abort(404, 'Order not found');
        }

        if ($order->order_type !== 'offline') {
            abort(404, 'PDF preview is only available for offline orders');
        }

        $order->load(['user', 'branch']);

        $buyerName = Auth::guard('admin')->user()?->name ?? '';

        return view('pdf.offline_order', OfflineOrderPdfData::forOrder(
            $order,
            $order->user,
            $buyerName
        ));
    }

    public function pending(ListOrdersRequest $request, ListPendingOffersAction $action): JsonResponse
    {
        $paginator = $action->execute(
            perPage: $request->perPage(),
            page: $request->page(),
            nameQuery: $request->nameQuery(),
            sourceFilter: $request->sourceFilter(),
            utmCampaignFilter: $request->utmCampaignFilter(),
            utmMediumFilter: $request->utmMediumFilter(),
            period: $request->period(),
            from: $request->from(),
            to: $request->to(),
            orderBy: $request->orderBy(),
            orderDir: $request->orderDir(),
        );

        return response()->json(new OrderResourceCollection(
            OrderResource::collection($paginator->items()),
            $paginator,
            [
                'sources' => $action->getAvailableSources(),
                'utm_campaigns' => $action->getAvailableUtmCampaigns(),
                'utm_mediums' => $action->getAvailableUtmMediums(),
                'branches' => $action->getAvailableBranches(),
            ]
        ));
    }

    public function paid(ListOrdersRequest $request, ListPaidOrdersAction $action): JsonResponse
    {
        $paginator = $action->execute(
            perPage: $request->perPage(),
            page: $request->page(),
            nameQuery: $request->nameQuery(),
            sourceFilter: $request->sourceFilter(),
            utmCampaignFilter: $request->utmCampaignFilter(),
            utmMediumFilter: $request->utmMediumFilter(),
            period: $request->period(),
            from: $request->from(),
            to: $request->to(),
            orderBy: $request->orderBy(),
            orderDir: $request->orderDir(),
        );

        return response()->json(new OrderResourceCollection(
            OrderResource::collection($paginator->items()),
            $paginator,
            [
                'sources' => $action->getAvailableSources(),
                'utm_campaigns' => $action->getAvailableUtmCampaigns(),
                'utm_mediums' => $action->getAvailableUtmMediums(),
                'branches' => $action->getAvailableBranches(),
            ]
        ));
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        try {
            $result = $this->createOrderAction->execute($request->validated());
            return response()->json($result, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateOrderRequest $request, int $id): JsonResponse
    {
        try {
            $order = $this->updateOrderAction->execute($id, $request->validated());
            return response()->json(new OrderResource($order));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateShipping(UpdateOrderShippingRequest $request, int $id): JsonResponse
    {
        try {
            $order = $this->updateOrderShippingAction->execute($id, $request->shipping());
            return response()->json(new OrderResource($order));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update shipping',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

