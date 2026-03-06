<?php

namespace App\Console\Commands;

use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Repositories\OrderRepositoryInterface;
use App\Domain\Orders\Services\OrderStatusUpdateService;
use App\Domain\Orders\Services\OrderHistoryService;
use App\Domain\Orders\Services\FedexService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Command to update order statuses based on FedEx tracking information
 *
 * REVIEW NOTES:
 * - This command runs every 3 hours (as scheduled in Kernel.php)
 * - It checks orders with status < ITEMS_RECEIVED created within last 3 months
 * - Updates status based on FedEx tracking:
 *   - KIT_REQUESTED -> IN_TRANSIT (when package is in transit)
 *   - KIT_REQUESTED/IN_TRANSIT -> ITEMS_RECEIVED (when package is delivered)
 * - Processes orders in batches of 30 (FedEx API recommendation)
 * - TODO: Currently FedEx tracking integration is not fully implemented
 *   Need to add getTrackingStatuses method to FedexService
 */
class UpdateInTransitOrderStatuses extends Command
{
    protected $signature = 'orders:update-in-transit-statuses {--date=}';

    protected $description = 'Update order statuses based on FedEx tracking information';

    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderStatusUpdateService $statusUpdateService,
        private readonly OrderHistoryService $historyService,
        private readonly FedexService $fedexService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Starting order status update based on FedEx tracking...');

        try {
            $date = $this->option('date')
                ? Carbon::parse($this->option('date'))
                : Carbon::now();

            $this->iterateThroughAllOrders($date);

            $this->info('Order status update completed successfully.');
            return Command::SUCCESS;
        } catch (Exception $e) {
            Log::error('Failed to update order statuses', [
                'command' => self::class,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->error('Failed to update order statuses: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Iterate through all orders in batches
     *
     * REVIEW: Processes orders in batches of 30 to comply with FedEx API limits
     */
    private function iterateThroughAllOrders(Carbon $date): void
    {
        $orderIdOffset = 0;
        $batchNumber = 0;

        do {
            $batchNumber++;
            $this->line("Processing batch #{$batchNumber}...");

            // Load orders for this batch
            $orders = $this->orderRepository->getOrdersForStatusUpdate($orderIdOffset, 30);
            $orderCount = $orders->count();

            if ($orderCount === 0) {
                break;
            }

            $this->info("Found {$orderCount} orders to check");

            // Extract tracking numbers
            $trackingNumbers = [];
            foreach ($orders as $order) {
                $trackingNum = $this->orderRepository->getTrackingNumber($order);
                if ($trackingNum) {
                    $trackingNumbers[$trackingNum] = $order;
                }
            }

            if (empty($trackingNumbers)) {
                $this->warn('No tracking numbers found in this batch');
                $orderIdOffset = $orders->last()->id;
                continue;
            }

            // Get tracking statuses from FedEx
            try {
                $trackingStatuses = $this->fedexService->getTrackingStatuses(array_keys($trackingNumbers));

                foreach ($trackingNumbers as $trackingNum => $order) {
                    $status = $trackingStatuses[$trackingNum] ?? null;
                    if ($status) {
                        $this->handleOrderStatusUpdate($order, $status);
                    }
                }
            } catch (Exception $e) {
                Log::warning('Failed to get tracking statuses', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $this->warn('Failed to get tracking statuses: ' . $e->getMessage());
            }

            $orderIdOffset = $orders->last()->id;
        } while ($orderCount > 0);
    }

    /**
     * Handle order status update based on tracking status
     *
     * REVIEW: This method would be called when tracking status is available
     * It checks if order status should be updated based on FedEx tracking status
     */
    /**
     * Handle order status update based on tracking status
     *
     * REVIEW: This method is called when tracking status is available from FedEx.
     * It checks if order status should be updated based on FedEx tracking status:
     * - KIT_REQUESTED -> IN_TRANSIT (when package is in transit)
     * - KIT_REQUESTED/IN_TRANSIT -> ITEMS_RECEIVED (when package is delivered)
     *
     * TODO: Implement when FedEx tracking API is fully integrated.
     * Requires getTrackingStatuses() method in FedexService that returns
     * objects with indicatesInTransit() and indicatesDelivered() methods.
     *
     * @param \App\Domain\Orders\Models\Order $order
     * @param object $trackingStatus Object with indicatesInTransit() and indicatesDelivered() methods
     */
    private function handleOrderStatusUpdate($order, $trackingStatus): void
    {
        $oldStatus = $order->status;

        // Update to IN_TRANSIT if package is in transit
        if ($order->status === OrderStatus::KIT_REQUESTED->value && $trackingStatus->indicatesInTransit()) {
            $this->statusUpdateService->updateOrderStatus(
                $order,
                OrderStatus::IN_TRANSIT,
                'Package in transit according to FedEx tracking'
            );

            $this->historyService->createAutomatedStatusHistory(
                $order,
                $oldStatus,
                OrderStatus::IN_TRANSIT->value,
                'Package in transit according to FedEx tracking',
                [
                    [
                        'key' => 'derivedCode',
                        'new' => $trackingStatus->getDerivedCode() ?? null,
                    ],
                ]
            );

            $this->line("Updated order #{$order->id} to IN_TRANSIT");
            return;
        }

        // Update to ITEMS_RECEIVED if package is delivered
        if (in_array($order->status, [OrderStatus::KIT_REQUESTED->value, OrderStatus::IN_TRANSIT->value])
            && $trackingStatus->indicatesDelivered()) {
            $this->statusUpdateService->updateOrderStatus(
                $order,
                OrderStatus::ITEMS_RECEIVED,
                'Package delivered according to FedEx tracking'
            );

            $this->historyService->createAutomatedStatusHistory(
                $order,
                $oldStatus,
                OrderStatus::ITEMS_RECEIVED->value,
                'Package delivered according to FedEx tracking',
                [
                    [
                        'key' => 'derivedCode',
                        'new' => $trackingStatus->getDerivedCode() ?? null,
                    ],
                ]
            );

            $this->line("Updated order #{$order->id} to ITEMS_RECEIVED");
        }
    }
}
