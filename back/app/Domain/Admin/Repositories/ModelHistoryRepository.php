<?php

namespace App\Domain\Admin\Repositories;

use App\Domain\Admin\Models\ModelHistory;
use Illuminate\Database\Eloquent\Collection;

class ModelHistoryRepository
{
    public function getRecentOrderStatusChanges(int $limit = 15): Collection
    {
        return ModelHistory::query()
            ->where('model_type', 'App\Models\Order')
            ->whereNot('user_type', ModelHistory::USER_TYPE_SYSTEM)
            ->where(function ($query) {
                $query->where('message', 'like', '%status%')
                      ->orWhere('message', 'like', '%changed from%to%');
            })
            ->with(['model:id', 'user:id,name'])
            ->orderBy('performed_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
