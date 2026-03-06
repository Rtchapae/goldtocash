<?php

namespace App\Console\Commands;

use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Repositories\OrderRepositoryInterface;
use App\Domain\Orders\Services\OrderStatusUpdateService;
use App\Domain\Orders\Services\OrderHistoryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Exception;

/**
 * Command to check sent offers and auto-accept them after 72 hours
 *
 * REVIEW NOTES:
 * - Runs every minute (as scheduled in Kernel.php)
 * - Checks orders with OFFER_SENT status (status = 4)
 * - Business rules:
 *   - After 72 hours (3 days): Auto-accept offer (status -> OFFER_ACCEPTED)
 *   - After 48 hours (2 days): Send warning email to user
 * - Sends email notifications to users
 * - Creates history records for audit trail
 */
class CheckSentOffers extends Command
{
    protected $signature = 'orders:check-sent-offers';

    protected $description = 'Check sent offers and auto-accept after 72 hours, send warnings after 48 hours';

    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderStatusUpdateService $statusUpdateService,
        private readonly OrderHistoryService $historyService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Checking sent offers...');

        try {
            $orders = $this->orderRepository->getOrdersByStatus(OrderStatus::OFFER_SENT->value);
            $this->info("Found {$orders->count()} orders with sent offers");

            $autoAcceptedCount = 0;
            $warningSentCount = 0;

            foreach ($orders as $order) {
                // Check if should auto-accept (72 hours passed)
                if ($this->statusUpdateService->shouldAutoAcceptOffer($order)) {
                    $this->handleAutoAccept($order);
                    $autoAcceptedCount++;
                }
                // Check if should send warning (48 hours passed)
                elseif ($this->statusUpdateService->shouldSendWarningEmail($order)) {
                    $this->handleWarningEmail($order);
                    $warningSentCount++;
                }
            }

            $this->info("Auto-accepted: {$autoAcceptedCount}, Warning emails sent: {$warningSentCount}");
            return Command::SUCCESS;
        } catch (Exception $e) {
            Log::error('Failed to check sent offers', [
                'command' => self::class,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->error('Failed to check sent offers: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Handle auto-acceptance of offer after 72 hours
     *
     * REVIEW: Business rule - if user doesn't respond within 72 hours,
     * offer is automatically accepted. This prevents orders from being stuck.
     */
    private function handleAutoAccept($order): void
    {
        // Load user relationship if not already loaded
        if (!$order->relationLoaded('user')) {
            $order->load('user');
        }

        $user = $order->user;
        if (!$user) {
            Log::warning('User not found for order', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
            ]);
            return;
        }

        $oldStatus = $order->status;

        // Update status
        $this->statusUpdateService->updateOrderStatus(
            $order,
            OrderStatus::OFFER_ACCEPTED,
            'Auto-accepted after 72 hours without user response'
        );

        // Create history record
        $this->historyService->createAutomatedStatusHistory(
            $order,
            $oldStatus,
            OrderStatus::OFFER_ACCEPTED->value,
            'Auto-accepted after 72 hours without user response'
        );

        // Send email notification
        try {
            Mail::send('emails.new_offer_for_user_accepted', compact('user', 'order'), function ($message) use ($user) {
                $message->to($user->email)
                    ->subject($user->first_name . ", Offer Accepted!");
            });
        } catch (Exception $e) {
            Log::warning('Failed to send auto-accept email', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        $this->line("Auto-accepted order #{$order->id} for user #{$user->id}");
    }

    /**
     * Handle warning email after 48 hours
     *
     * REVIEW: Sends reminder email to user that they have 24 hours left
     * to respond before offer is auto-accepted.
     */
    private function handleWarningEmail($order): void
    {
        // Load user relationship if not already loaded
        if (!$order->relationLoaded('user')) {
            $order->load('user');
        }

        $user = $order->user;
        if (!$user) {
            return;
        }

        try {
            Mail::send('emails.new_offer_for_user_warning', compact('user', 'order'), function ($message) use ($user) {
                $message->to($user->email)
                    ->subject($user->first_name . ", We are still waiting for your response!");
            });

            $this->line("Sent warning email for order #{$order->id}");
        } catch (Exception $e) {
            Log::warning('Failed to send warning email', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

