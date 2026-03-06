<?php

namespace App\Domain\Admin\Controllers;

use App\Domain\Messages\Repositories\MessageRepositoryInterface;
use App\Domain\Admin\Requests\SendMessageRequest;
use App\Domain\Admin\Resources\ChatResource;
use App\Domain\Users\Models\User;
use App\Http\Controllers\Controller;
use App\Jobs\SendNewMessageEmailJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct(
        private readonly MessageRepositoryInterface $messageRepository,
    ) {
    }

    public function index(): JsonResponse
    {
        /** @var User|null $adminUser */
        $adminUser = Auth::guard('admin')->user();

        $threads = $this->messageRepository->getSupportThreadsWithParticipants();

        $chats = $threads->map(function ($thread) use ($adminUser) {
            $participant = $thread->participants->first();
            $user = $participant?->user;

            $lastMessage = $thread->messages->first();

            $unreadCount = $this->messageRepository->getUnreadMessagesCountForThread(
                $thread->id,
                $adminUser?->id ?? 0
            );

            return [
                'thread_id' => $thread->id,
                'user_id' => $user?->id,
                'user_name' => $user?->name ?? ($user?->first_name . ' ' . $user?->last_name) ?? 'Unknown',
                'user_email' => $user?->email ?? '',
                'unread_count' => (int) $unreadCount,
                'last_message' => $lastMessage ? [
                    'text' => $lastMessage->body,
                    'created_at' => $lastMessage->created_at->toISOString(),
                ] : null,
                'last_message_date' => $lastMessage ? $lastMessage->created_at->toISOString() : $thread->updated_at->toISOString(),
            ];
        });

        $chats = $chats->sortByDesc(function ($chat) {
            return [
                $chat['unread_count'] > 0 ? 1 : 0,
                $chat['last_message_date'],
            ];
        })->values();

        return response()->json([
            'status' => true,
            'chats' => ChatResource::collection($chats->values()),
        ]);
    }

    public function show(int $threadId): JsonResponse
    {
        $thread = $this->messageRepository->findThreadById($threadId);

        if (!$thread) {
            return response()->json([
                'status' => false,
                'message' => 'Thread not found',
            ], 404);
        }

        $messages = $this->messageRepository->getThreadMessages($thread);

        $participant = $thread->participants->first();
        $user = $participant?->user;

        $formattedMessages = $messages->map(function ($message) use ($user) {
            return [
                'id' => $message->id,
                'text' => $message->body,
                'isSent' => $message->user_id !== $user?->id,
                'isRead' => (bool) $message->read,
                'createdAt' => $message->created_at->toISOString(),
            ];
        })->toArray();

        if ($user) {
            $this->messageRepository->markUserMessagesAsRead($thread->id, $user->id);
        }

        return response()->json([
            'status' => true,
            'thread_id' => $thread->id,
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name ?? ($user->first_name . ' ' . $user->last_name) ?? 'Unknown',
                'email' => $user->email ?? '',
            ] : null,
            'messages' => $formattedMessages,
        ]);
    }

    public function send(SendMessageRequest $request, int $threadId): JsonResponse
    {
        $thread = $this->messageRepository->findThreadById($threadId);

        if (!$thread) {
            return response()->json([
                'status' => false,
                'message' => 'Thread not found',
            ], 404);
        }

        /** @var User $adminUser */
        $adminUser = Auth::guard('admin')->user();

        if (!$adminUser) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $message = $this->messageRepository->createMessage($thread, $adminUser, $request->text());

        $participant = $thread->participants->first();
        $user = $participant?->user;

        if ($user && $user->email) {
            try {
                SendNewMessageEmailJob::dispatch($user);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to dispatch new message email job', [
                    'user_id' => $user->id,
                    'thread_id' => $thread->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => [
                'id' => $message->id,
                'text' => $message->body,
                'isSent' => true,
                'isRead' => false,
                'createdAt' => $message->created_at->toISOString(),
            ],
        ]);
    }
}

