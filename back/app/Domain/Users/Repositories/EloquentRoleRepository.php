<?php

namespace App\Domain\Users\Repositories;

use App\Domain\Users\Models\Role;
use App\Domain\Users\Enums\UserRole;

class EloquentRoleRepository implements RoleRepositoryInterface
{
    public function findByName(string $name): ?Role
    {
        return Role::where('name', $name)->first();
    }

    public function findByEnum(UserRole $role): ?Role
    {
        return $this->findByName($role->value);
    }
}

