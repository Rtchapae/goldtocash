<?php

namespace App\Domain\Messages\Controllers;

use App\Domain\Messages\Actions\GetMessagesAction;
use App\Domain\Messages\Actions\SendMessageAction;
use App\Domain\Messages\Actions\MarkMessagesAsReadAction;
use App\Domain\Messages\Requests\SendMessageRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class FrontMessageController extends Controller
{
    public function index(GetMessagesAction $action): JsonResponse
    {
        $data = $action->execute();

        return response()->json([
            'status' => true,
            'thread_id' => $data['thread_id'],
            'messages' => $data['messages'],
        ]);
    }

    public function send(SendMessageRequest $request, SendMessageAction $action): JsonResponse
    {
        $message = $action->execute($request->input('text'));

        return response()->json([
            'status' => true,
            'message' => $message,
        ]);
    }

    public function markAsRead(MarkMessagesAsReadAction $action): JsonResponse
    {
        $action->execute();

        return response()->json([
            'status' => true,
            'message' => 'Messages marked as read',
        ]);
    }
}


