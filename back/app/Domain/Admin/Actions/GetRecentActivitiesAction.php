<?php

namespace App\Domain\Admin\Actions;

use App\Domain\Admin\Repositories\ModelHistoryRepository;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Models\Order;

class GetRecentActivitiesAction
{
    public function __construct(
        private readonly ModelHistoryRepository $modelHistoryRepository,
    ) {}

    public function execute(): array
    {
        $kitRequests = Order::query()
            ->with(['user:id,first_name,last_name'])
            ->select(['id', 'user_id', 'created_at', 'amount'])
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'user_name' => $order->user ? trim($order->user->first_name . ' ' . $order->user->last_name) : 'Unknown',
                    'amount' => $order->amount,
                    'created_at' => $order->created_at->format('M j, Y H:i'),
                ];
            });

        $statusChanges = $this->modelHistoryRepository
            ->getRecentOrderStatusChanges(15)
            ->map(function ($history) {
                $oldStatus = 'Unknown';
                $newStatus = 'Unknown';
                $message = $history->message ?? '';

                if (isset($history->meta['old_status'], $history->meta['new_status'])) {
                    $oldStatus = $history->meta['old_status'];
                    $newStatus = $history->meta['new_status'];
                }
                elseif (!empty($history->meta) && is_array($history->meta)) {
                    foreach (is_array($history->meta) ? $history->meta : [] as $item) {
                        if (is_array($item) && ($item['key'] ?? null) === 'status' && isset($item['old'], $item['new'])) {
                            $oldStatus = $item['old'];
                            $newStatus = $item['new'];
                            break;
                        }
                    }
                }

                if ($oldStatus === 'Unknown' && $newStatus === 'Unknown' && preg_match('/from (\w+) to (\w+)/i', $message, $matches)) {
                    $oldStatus = $matches[1];
                    $newStatus = $matches[2];
                }
                elseif ($oldStatus === 'Unknown' && preg_match('/Updated status to match FedEx/i', $message)) {
                    $oldStatus = 'Previous';
                    $newStatus = 'Updated';
                }

                $oldStatusLabel = $this->statusToLabel($oldStatus);
                $newStatusLabel = $this->statusToLabel($newStatus);

                return [
                    'order_id' => $history->model_id,
                    'user_name' => $history->model && $history->model->user
                        ? trim($history->model->user->first_name . ' ' . $history->model->user->last_name)
                        : 'Unknown',
                    'old_status' => $oldStatusLabel,
                    'new_status' => $newStatusLabel,
                    'changed_by' => $history->user ? $history->user->name : 'System',
                    'changed_at' => $history->performed_at->format('M j, Y H:i'),
                ];
            });

        $offlineTransactions = Order::query()
            ->where('order_type', 'offline')
            ->with(['user:id,first_name,last_name', 'branch:id,name'])
            ->select(['id', 'user_id', 'branch_id', 'amount', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'user_name' => $order->user ? trim($order->user->first_name . ' ' . $order->user->last_name) : 'Unknown',
                    'branch' => $order->branch ? $order->branch->name : 'Unknown',
                    'amount' => $order->amount,
                    'created_at' => $order->created_at->format('M j, Y H:i'),
                ];
            });

        return [
            'kit_requests' => $kitRequests->toArray(),
            'status_changes' => $statusChanges->toArray(),
            'offline_transactions' => $offlineTransactions->toArray(),
        ];
    }

    private function statusToLabel(mixed $status): string
    {
        if (is_numeric($status)) {
            $enum = OrderStatus::tryFrom((int) $status);
            return $enum?->label() ?? (string) $status;
        }
        return (string) $status;
    }
}
