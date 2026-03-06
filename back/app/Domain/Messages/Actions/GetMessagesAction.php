<?php

namespace App\Domain\Messages\Actions;

use App\Domain\Messages\Repositories\MessageRepositoryInterface;
use App\Domain\Users\Models\User;
use Illuminate\Support\Facades\Auth;

class GetMessagesAction
{
    public function __construct(
        private readonly MessageRepositoryInterface $messageRepository,
    ) {
    }

    public function execute(): array
    {
        /** @var User|null $user */
        $user = Auth::guard('front')->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $thread = $this->messageRepository->getOrCreateSupportThread($user);

        $messages = $this->messageRepository->getThreadMessages($thread);

        $formattedMessages = $messages->map(function ($message) use ($user) {
            return [
                'id' => $message->id,
                'text' => $message->body,
                'isSent' => $message->user_id === $user->id,
                'isRead' => (bool) $message->read,
                'createdAt' => $message->created_at->toISOString(),
            ];
        })->toArray();

        return [
            'thread_id' => $thread->id,
            'messages' => $formattedMessages,
        ];
    }
}


