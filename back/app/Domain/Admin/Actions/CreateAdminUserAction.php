<?php

namespace App\Domain\Admin\Actions;

use App\Domain\Users\Models\User;
use App\Domain\Users\Enums\UserRole;
use App\Domain\Users\Repositories\UserRepositoryInterface;
use App\Domain\Users\Repositories\RoleRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class CreateAdminUserAction
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RoleRepositoryInterface $roleRepository,
    ) {
    }

    public function execute(array $data): User
    {
        $roleName = $data['role'];

        if (!in_array($roleName, [UserRole::ADMIN->value, UserRole::MANAGER->value])) {
            throw new Exception('Invalid role. Must be admin or manager.');
        }

        $roleEnum = UserRole::from($roleName);
        $role = $this->roleRepository->findByEnum($roleEnum);
        if (!$role) {
            throw new Exception("Role '{$roleName}' not found.");
        }

        if ($roleName === UserRole::MANAGER->value && empty($data['branch_id'])) {
            throw new Exception('Branch is required for manager role.');
        }

        if ($this->userRepository->findByEmail($data['email'])) {
            throw new Exception('Email already exists.');
        }

        try {
            $userData = [
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => $role->id,
                'first_name' => $data['first_name'] ?? null,
                'last_name' => $data['last_name'] ?? null,
                'name' => $data['name'] ?? ($data['first_name'] . ' ' . $data['last_name']) ?? null,
            ];

            if ($roleName === UserRole::MANAGER->value) {
                $userData['branch_id'] = $data['branch_id'];
            }

            $user = $this->userRepository->create($userData);

            Log::info('Admin user created', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $roleName,
            ]);

            return $user->load(['roleRelation', 'branch']);
        } catch (Exception $e) {
            Log::error('Failed to create admin user', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }
}

