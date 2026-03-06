<?php

namespace App\Domain\Users\Repositories;

use App\Domain\Users\Models\User;
use App\Domain\Users\Models\Role;
use App\Domain\Users\Enums\UserRole;
use App\Jobs\SendPasswordEmailJob;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository,
    ) {
    }
    public function paginateForAdmin(
        int $perPage = 15,
        ?string $search = null,
        ?int $excludeUserId = null,
    ): LengthAwarePaginator {
        $query = User::query()->orderByDesc('id');

        if ($excludeUserId !== null) {
            $query->where('id', '!=', $excludeUserId);
        }

        if ($search !== null && $search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        return $query->paginate($perPage);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findByEmailOrPhone(string $email, string $phone): ?User
    {
        return User::where('email', $email)
            ->orWhere('phone', $phone)
            ->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function sendPasswordEmail(User $user, string $password): void
    {
        SendPasswordEmailJob::dispatch($user, $password);
    }

    public function update(User $user, array $data): User
    {
        $user->fill($data);
        $user->save();

        return $user->refresh();
    }

    public function getAdmins(): \Illuminate\Support\Collection
    {
        $adminRole = $this->roleRepository->findByEnum(UserRole::ADMIN);

        if (!$adminRole) {
            return collect([]);
        }

        return User::where('role_id', $adminRole->id)->get();
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByIdWith(int $id, array $relations = []): ?User
    {
        $query = User::query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->find($id);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function listAdminUsers(
        int $perPage = 15,
        int $page = 1,
        ?string $search = null,
        ?string $role = null
    ): \Illuminate\Contracts\Pagination\LengthAwarePaginator {
        $adminRole = $this->roleRepository->findByEnum(UserRole::ADMIN);
        $managerRole = $this->roleRepository->findByEnum(UserRole::MANAGER);

        $query = User::query()
            ->whereIn('role_id', array_filter([$adminRole?->id, $managerRole?->id]))
            ->with(['roleRelation', 'branch'])
            ->orderByDesc('id');

        if ($role === 'admin' && $adminRole) {
            $query->where('role_id', $adminRole->id);
        } elseif ($role === 'manager' && $managerRole) {
            $query->where('role_id', $managerRole->id);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%');
            });
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}


