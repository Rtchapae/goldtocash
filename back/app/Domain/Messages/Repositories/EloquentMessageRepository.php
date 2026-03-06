<?php

namespace App\Domain\Messages\Repositories;

use App\Domain\Messages\Models\Thread;
use App\Domain\Messages\Models\Message;
use App\Domain\Users\Models\User;
use App\Domain\Users\Repositories\RoleRepositoryInterface;
use App\Domain\Users\Enums\UserRole;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentMessageRepository implements MessageRepositoryInterface
{
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository,
    ) {
    }
    public function getOrCreateSupportThread(User $user): Thread
    {
        $thread = Thread::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->first();

        if (!$thread) {
            $thread = Thread::create([
                'subject' => 'Technical Support',
                'slug' => md5('support_' . $user->id . '_' . time()),
            ]);

            DB::table('participants')->insert([
                'thread_id' => $thread->id,
                'user_id' => $user->id,
                'starred' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $thread;
    }

    public function getThreadMessages(Thread $thread): Collection
    {
        return Message::where('thread_id', $thread->id)
            ->with('user:id,name,email')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function createMessage(Thread $thread, User $user, string $body): Message
    {
        return Message::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => $body,
            'read' => null,
        ]);
    }

    public function markAsRead(Thread $thread, User $user): void
    {
        Message::where('thread_id', $thread->id)
            ->where('user_id', '!=', $user->id)
            ->update(['read' => true]);

        DB::table('participants')
            ->where('thread_id', $thread->id)
            ->where('user_id', $user->id)
            ->update(['last_read' => now()]);
    }

    public function getUnreadMessagesCountForAdmin(int $adminId): int
    {
        return Message::query()
            ->where('messages.user_id', '!=', $adminId)
            ->where(function ($query) {
                $query->whereNull('messages.read')
                    ->orWhere('messages.read', 0)
                    ->orWhere('messages.read', false);
            })
            ->count();
    }

    public function getSupportThreadsWithParticipants(): Collection
    {
        return Thread::query()
            ->with(['participants.user:id,name,email,first_name,last_name'])
            ->with(['messages' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(1);
            }])
            ->get();
    }

    public function findThreadById(int $threadId): ?Thread
    {
        return Thread::find($threadId);
    }

    public function getUnreadMessagesCountForThread(int $threadId, int $adminId): int
    {
        return Message::where('thread_id', $threadId)
            ->where('user_id', '!=', $adminId)
            ->where(function ($query) {
                $query->whereNull('read')
                    ->orWhere('read', 0)
                    ->orWhere('read', false);
            })
            ->count();
    }

    public function markUserMessagesAsRead(int $threadId, int $userId): void
    {
        Message::where('thread_id', $threadId)
            ->where('user_id', $userId)
            ->update(['read' => true]);
    }

    public function markAdminMessagesAsRead(int $threadId, int $userId): void
    {
        $adminRole = $this->roleRepository->findByEnum(UserRole::ADMIN);

        if (!$adminRole) {
            return;
        }

        $adminIds = User::where('role_id', $adminRole->id)->pluck('id');

        Message::where('thread_id', $threadId)
            ->whereIn('user_id', $adminIds)
            ->where('user_id', '!=', $userId)
            ->update(['read' => true]);

        DB::table('participants')
            ->where('thread_id', $threadId)
            ->where('user_id', $userId)
            ->update(['last_read' => now()]);
    }
}

