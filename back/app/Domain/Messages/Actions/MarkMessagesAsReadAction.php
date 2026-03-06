<?php

namespace App\Domain\Messages\Actions;

use App\Domain\Messages\Repositories\MessageRepositoryInterface;
use App\Domain\Users\Models\User;
use Illuminate\Support\Facades\Auth;

class MarkMessagesAsReadAction
{
    public function __construct(
        private readonly MessageRepositoryInterface $messageRepository,
    ) {
    }

    public function execute(): void
    {
        /** @var User|null $user */
        $user = Auth::guard('front')->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $thread = $this->messageRepository->getOrCreateSupportThread($user);

        $this->messageRepository->markAdminMessagesAsRead($thread->id, $user->id);
    }
}

