<?php

namespace App\Console\Commands;

use App\Domain\Admin\Services\NotificationService;
use App\Domain\Users\Enums\UserRole;
use Illuminate\Console\Command;

class CreateTestNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:create-test {admin_id?}';

    /**
     * The console command description.
     */
    protected $description = 'Create test notifications for admin user';

    public function __construct(
        private readonly NotificationService $notificationService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $adminId = $this->argument('admin_id');

        // If no admin_id provided, show available admins
        if (!$adminId) {
            $this->showAvailableAdmins();
            return;
        }

        $adminId = (int) $adminId;
        $this->createNotificationsForAdmin($adminId);
    }

    /**
     * Show available admin users
     */
    private function showAvailableAdmins(): void
    {
        $admins = \App\Domain\Users\Models\User::with('roleRelation')
            ->where(function ($query) {
                $query->whereHas('roleRelation', function ($q) {
                    $q->whereIn('name', [UserRole::ADMIN->value, UserRole::MANAGER->value]);
                });
            })
            ->get();

        if ($admins->isEmpty()) {
            $this->error('No admin or manager users found in the database!');
            $this->info('You may need to create admin users first.');
            return;
        }

        $this->info('Available admin/manager users:');
        $this->table(
            ['ID', 'Email', 'Name', 'Role'],
            $admins->map(function ($admin) {
                return [
                    $admin->id,
                    $admin->email,
                    $admin->first_name . ' ' . $admin->last_name,
                    $admin->roleRelation?->display_name ?? 'Unknown'
                ];
            })->toArray()
        );

        $this->info('Run: php artisan notifications:create-test {id}');
    }

    /**
     * Create test notifications for specific admin
     */
    private function createNotificationsForAdmin(int $adminId): void
    {
        // Check if user exists and is admin/manager
        $user = \App\Domain\Users\Models\User::find($adminId);

        if (!$user) {
            $this->error("User with ID {$adminId} does not exist!");
            return;
        }

        if (!$user->isAdmin() && !$user->isManager()) {
            $this->error("User with ID {$adminId} is not an admin or manager!");
            return;
        }

        $this->info("Creating test notifications for user: {$user->email} ({$user->roleRelation?->display_name})");

        // Create test notifications
        $this->notificationService->createAppraisalRequestNotification(
            $adminId,
            'John Doe',
            'APP-2024-001'
        );

        $this->notificationService->createOfferResponseNotification(
            $adminId,
            'Jane Smith',
            'APP-2024-002',
            '$500.00',
            'accepted'
        );

        $this->notificationService->createStatusChangeNotification(
            $adminId,
            'Bob Johnson',
            'KIT-2024-003',
            'In Transit'
        );

        $this->notificationService->createOfflineTransactionNotification(
            $adminId,
            'Alice Brown',
            '$1,250.00'
        );

        $this->info('Test notifications created successfully!');
        $this->info('You can now check the admin panel notifications dropdown.');
    }
}
