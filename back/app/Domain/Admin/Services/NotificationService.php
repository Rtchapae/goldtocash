<?php

namespace App\Domain\Admin\Services;

use App\Domain\Admin\Models\Notification;
use App\Domain\Users\Models\User;

class NotificationService
{
    public function createAppraisalRequestNotification(
        int $adminUserId,
        string $userName,
        string $orderNumber,
        ?string $userAvatar = null
    ): Notification {
        return Notification::create([
            'admin_user_id' => $adminUserId,
            'type' => 'appraisal_request',
            'title' => 'New Appraisal Request',
            'message' => "New appraisal request from {$userName} for order #{$orderNumber}",
            'data' => [
                'user_name' => $userName,
                'user_avatar' => $userAvatar,
                'order_number' => $orderNumber,
            ],
        ]);
    }

    public function createOfferResponseNotification(
        int $adminUserId,
        string $userName,
        string $orderNumber,
        string $amount,
        string $action, // 'accepted' or 'denied'
        ?string $userAvatar = null
    ): Notification {
        $actionText = $action === 'accepted' ? 'accepted' : 'denied';

        return Notification::create([
            'admin_user_id' => $adminUserId,
            'type' => 'offer_response',
            'title' => "Offer {$actionText}",
            'message' => "{$userName} has {$actionText} the offer of {$amount} for order #{$orderNumber}",
            'data' => [
                'user_name' => $userName,
                'user_avatar' => $userAvatar,
                'order_number' => $orderNumber,
                'amount' => $amount,
                'action' => $action,
            ],
        ]);
    }

    public function createStatusChangeNotification(
        int $adminUserId,
        string $userName,
        string $kitNumber,
        string $status,
        ?string $userAvatar = null
    ): Notification {
        return Notification::create([
            'admin_user_id' => $adminUserId,
            'type' => 'status_change',
            'title' => 'Status Update',
            'message' => "{$userName}'s kit #{$kitNumber} status changed to: {$status}",
            'data' => [
                'user_name' => $userName,
                'user_avatar' => $userAvatar,
                'kit_number' => $kitNumber,
                'status' => $status,
            ],
        ]);
    }

    public function createOfflineTransactionNotification(
        int $adminUserId,
        string $userName,
        string $amount,
        ?string $userAvatar = null
    ): Notification {
        return Notification::create([
            'admin_user_id' => $adminUserId,
            'type' => 'offline_transaction',
            'title' => 'Offline Transaction',
            'message' => "New offline transaction recorded for {$userName} - Amount: {$amount}",
            'data' => [
                'user_name' => $userName,
                'user_avatar' => $userAvatar,
                'amount' => $amount,
            ],
        ]);
    }

    public function getAllAdminUsers(): array
    {
        return User::where(function ($query) {
            $query->where('role_id', 1)
                  ->orWhere('role_id', 2);
        })->pluck('id')->toArray();
    }

    public function createNotificationForAllAdmins(
        string $type,
        string $title,
        string $message,
        array $data = []
    ): void {
        $adminUserIds = $this->getAllAdminUsers();

        foreach ($adminUserIds as $adminUserId) {
            Notification::create([
                'admin_user_id' => $adminUserId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
            ]);
        }
    }
}




