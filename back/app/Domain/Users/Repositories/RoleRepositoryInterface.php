<?php

namespace App\Domain\Users\Repositories;

use App\Domain\Users\Models\Role;
use App\Domain\Users\Enums\UserRole;

interface RoleRepositoryInterface
{
    public function findByName(string $name): ?Role;

    public function findByEnum(UserRole $role): ?Role;
}

