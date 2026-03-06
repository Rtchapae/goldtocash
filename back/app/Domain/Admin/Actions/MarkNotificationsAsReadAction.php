<?php

namespace App\Domain\Admin\Actions;

use App\Domain\Admin\Models\Notification;

class MarkNotificationsAsReadAction
{
    public function execute(int $adminUserId, ?int $notificationId = null): void
    {
        $query = Notification::where('admin_user_id', $adminUserId)
            ->where('is_read', false);

        if ($notificationId) {
            $query->where('id', $notificationId);
        }

        $query->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
}




