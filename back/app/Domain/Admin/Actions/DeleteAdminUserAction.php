<?php

namespace App\Domain\Admin\Actions;

use App\Domain\Users\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class DeleteAdminUserAction
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function execute(int $userId): void
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new Exception('User not found.');
        }

        if (!$user->isAdmin() && !$user->isManager()) {
            throw new Exception('User is not an admin or manager.');
        }

        $currentUser = Auth::guard('admin')->user();
        if ($currentUser && $currentUser->id === $userId) {
            throw new Exception('You cannot delete your own account.');
        }

        try {
            $this->userRepository->delete($user);

            Log::info('Admin user deleted', [
                'user_id' => $userId,
                'email' => $user->email,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to delete admin user', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}

