<?php

namespace App\Domain\Admin\Actions;

use App\Domain\Users\Models\User;
use App\Domain\Users\Enums\UserRole;
use App\Domain\Users\Repositories\UserRepositoryInterface;
use App\Domain\Users\Repositories\RoleRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class UpdateAdminUserAction
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RoleRepositoryInterface $roleRepository,
    ) {
    }

    public function execute(int $userId, array $data): User
    {
        $user = $this->userRepository->findByIdWith($userId, ['roleRelation', 'branch']);

        if (!$user) {
            throw new Exception('User not found.');
        }

        if (!$user->isAdmin() && !$user->isManager()) {
            throw new Exception('User is not an admin or manager.');
        }

        try {
            if (isset($data['email'])) {
                $existingUser = $this->userRepository->findByEmail($data['email']);
                if ($existingUser && $existingUser->id !== $userId) {
                    throw new Exception('Email already exists.');
                }
                $user->email = $data['email'];
            }

            if (isset($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            $updateData = [];

            if (isset($data['email'])) {
                $updateData['email'] = $data['email'];
            }

            if (isset($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            if (isset($data['first_name'])) {
                $updateData['first_name'] = $data['first_name'];
            }
            if (isset($data['last_name'])) {
                $updateData['last_name'] = $data['last_name'];
            }
            if (isset($data['name'])) {
                $updateData['name'] = $data['name'];
            }

            if (isset($data['role'])) {
                $roleName = $data['role'];

                if (!in_array($roleName, [UserRole::ADMIN->value, UserRole::MANAGER->value])) {
                    throw new Exception('Invalid role. Must be admin or manager.');
                }

                $roleEnum = UserRole::from($roleName);
                $role = $this->roleRepository->findByEnum($roleEnum);
                if (!$role) {
                    throw new Exception("Role '{$roleName}' not found.");
                }

                $updateData['role_id'] = $role->id;

                if ($roleName === UserRole::MANAGER->value) {
                    if (isset($data['branch_id'])) {
                        $updateData['branch_id'] = $data['branch_id'];
                    } elseif (!$user->branch_id) {
                        throw new Exception('Branch is required for manager role.');
                    }
                } else {
                    $updateData['branch_id'] = null;
                }
            } else {
                if (isset($data['branch_id'])) {
                    if ($user->isManager()) {
                        $updateData['branch_id'] = $data['branch_id'];
                    } else {
                        $updateData['branch_id'] = null;
                    }
                }
            }

            $this->userRepository->update($user, $updateData);

            Log::info('Admin user updated', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return $this->userRepository->findByIdWith($user->id, ['roleRelation', 'branch']);
        } catch (Exception $e) {
            Log::error('Failed to update admin user', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }
}

