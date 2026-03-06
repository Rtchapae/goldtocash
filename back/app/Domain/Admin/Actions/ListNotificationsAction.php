<?php

namespace App\Domain\Admin\Actions;

use App\Domain\Admin\Models\Notification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ListNotificationsAction
{
    public function execute(
        int $adminUserId,
        int $limit = 20,
        int $offset = 0
    ): Collection {
        return Notification::where('admin_user_id', $adminUserId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();
    }
}
