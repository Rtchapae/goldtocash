<?php

namespace App\Domain\Admin\Controllers;

use App\Domain\Admin\Actions\ListNotificationsAction;
use App\Domain\Admin\Actions\MarkNotificationsAsReadAction;
use App\Domain\Admin\Actions\GetNotificationCountAction;
use App\Domain\Admin\Requests\ListNotificationsRequest;
use App\Domain\Admin\Resources\NotificationResource;
use App\Domain\Admin\Resources\NotificationResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController
{
    public function __construct(
        private readonly ListNotificationsAction $listAction,
        private readonly MarkNotificationsAsReadAction $markAsReadAction,
        private readonly GetNotificationCountAction $getCountAction
    ) {}

    public function index(ListNotificationsRequest $request): JsonResponse
    {
        $adminUser = Auth::guard('admin')->user();
        $notifications = $this->listAction->execute(
            adminUserId: $adminUser->id,
            limit: $request->input('limit', 20),
            offset: $request->input('offset', 0)
        );

        return response()->json(new NotificationResourceCollection($notifications));
    }

    public function markAllAsRead(): JsonResponse
    {
        $adminUser = Auth::guard('admin')->user();
        $this->markAsReadAction->execute(adminUserId: $adminUser->id);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function markAsRead(int $id): JsonResponse
    {
        $adminUser = Auth::guard('admin')->user();
        $this->markAsReadAction->execute(adminUserId: $adminUser->id, notificationId: $id);

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function getCount(): JsonResponse
    {
        $adminUser = Auth::guard('admin')->user();
        $counts = $this->getCountAction->execute(adminUserId: $adminUser->id);

        return response()->json($counts);
    }
}




