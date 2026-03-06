<?php

namespace App\Domain\Admin\Actions;

use App\Domain\Admin\Models\Notification;

class GetNotificationCountAction
{
    public function execute(int $adminUserId): array
    {
        $total = Notification::where('admin_user_id', $adminUserId)->count();

        $unread = Notification::where('admin_user_id', $adminUserId)
            ->where('is_read', false)
            ->count();

        return [
            'total' => $total,
            'unread' => $unread,
        ];
    }
}




