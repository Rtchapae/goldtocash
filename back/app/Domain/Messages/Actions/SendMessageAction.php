<?php

namespace App\Domain\Messages\Actions;

use App\Domain\Messages\Repositories\MessageRepositoryInterface;
use App\Domain\Users\Models\User;
use Illuminate\Support\Facades\Auth;

class SendMessageAction
{
    public function __construct(
        private readonly MessageRepositoryInterface $messageRepository,
    ) {
    }

    public function execute(string $body): array
    {
        /** @var User|null $user */
        $user = Auth::guard('front')->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $thread = $this->messageRepository->getOrCreateSupportThread($user);

        $message = $this->messageRepository->createMessage($thread, $user, $body);

        return [
            'id' => $message->id,
            'text' => $message->body,
            'isSent' => true,
            'isRead' => false,
            'createdAt' => $message->created_at->toISOString(),
        ];
    }
}


