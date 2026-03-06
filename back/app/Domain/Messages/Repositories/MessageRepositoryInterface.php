<?php

namespace App\Domain\Messages\Repositories;

use App\Domain\Messages\Models\Thread;
use App\Domain\Messages\Models\Message;
use App\Domain\Users\Models\User;
use Illuminate\Support\Collection;

interface MessageRepositoryInterface
{
    public function getOrCreateSupportThread(User $user): Thread;

    public function getThreadMessages(Thread $thread): Collection;

    public function createMessage(Thread $thread, User $user, string $body): Message;

    public function markAsRead(Thread $thread, User $user): void;

    public function getUnreadMessagesCountForAdmin(int $adminId): int;

    public function getSupportThreadsWithParticipants(): Collection;

    public function findThreadById(int $threadId): ?Thread;

    public function getUnreadMessagesCountForThread(int $threadId, int $adminId): int;

    public function markUserMessagesAsRead(int $threadId, int $userId): void;

    public function markAdminMessagesAsRead(int $threadId, int $userId): void;
}


