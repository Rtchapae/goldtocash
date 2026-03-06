<?php

namespace App\Console\Commands;

use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Repositories\OrderRepositoryInterface;
use App\Domain\Users\Repositories\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Exception;

/**
 * Command to check accepted offers and notify admins
 * 
 * REVIEW NOTES:
 * - Runs every minute (as scheduled in Kernel.php)
 * - Checks orders with OFFER_ACCEPTED status (status = 5)
 * - Business rule: After 72 hours (3 days) of acceptance, notify admins
 * - Sends email notifications to all admin users
 * - This helps admins track which orders need payment processing
 */
class CheckAcceptedOffers extends Command
{
    protected $signature = 'orders:check-accepted-offers';

    protected $description = 'Check accepted offers and notify admins after 72 hours';

    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly UserRepositoryInterface $userRepository,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Checking accepted offers...');

        try {
            $orders = $this->orderRepository->getOrdersByStatus(OrderStatus::OFFER_ACCEPTED->value);
            $this->info("Found {$orders->count()} orders with accepted offers");

            $notifiedCount = 0;

            foreach ($orders as $order) {
                // Check if 72 hours have passed since acceptance
                if ($this->shouldNotifyAdmins($order)) {
                    $this->handleAdminNotification($order);
                    $notifiedCount++;
                }
            }

            $this->info("Admin notifications sent: {$notifiedCount}");
            return Command::SUCCESS;
        } catch (Exception $e) {
            Log::error('Failed to check accepted offers', [
                'command' => self::class,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->error('Failed to check accepted offers: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Check if admins should be notified (72 hours after acceptance)
     * 
     * REVIEW: Business rule - notify admins exactly 72 hours after offer acceptance
     * This gives admins a reminder to process payment for accepted offers.
     */
    private function shouldNotifyAdmins($order): bool
    {
        $threeDaysAgo = $order->updated_at->copy()->addDays(3);
        $threeDaysAgoPlusOneMinute = $threeDaysAgo->copy()->addMinute();

        // Notify if exactly 72 hours have passed (within 1 minute window)
        return $threeDaysAgo->isPast() && $threeDaysAgoPlusOneMinute->isFuture();
    }

    /**
     * Send notification emails to all admins
     * 
     * REVIEW: Sends email to all users with admin role (role is not null)
     * This ensures all admins are aware of accepted offers that need processing.
     */
    private function handleAdminNotification($order): void
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

        $admins = $this->userRepository->getAdmins();

        if ($admins->isEmpty()) {
            Log::warning('No admin users found for notification', [
                'order_id' => $order->id,
            ]);
            return;
        }

        foreach ($admins as $admin) {
            try {
                Mail::send('emails.new_offer_for_admin_accepted', compact('user', 'order'), function ($message) use ($admin, $order) {
                    $message->to($admin->email)
                        ->subject("Order #{$order->id} Accepted Offer");
                });

                $this->line("Sent notification to admin #{$admin->id} about order #{$order->id}");
            } catch (Exception $e) {
                Log::warning('Failed to send admin notification', [
                    'order_id' => $order->id,
                    'admin_id' => $admin->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}

