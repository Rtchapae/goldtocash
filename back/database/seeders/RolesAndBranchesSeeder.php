<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Users\Models\Role;
use App\Domain\Users\Models\Branch;
use App\Domain\Users\Enums\UserRole;
use Illuminate\Support\Facades\DB;

class RolesAndBranchesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles using enum
        foreach (UserRole::cases() as $roleEnum) {
            Role::updateOrCreate(
                ['name' => $roleEnum->value],
                [
                    'name' => $roleEnum->value,
                    'display_name' => $roleEnum->label(),
                    'description' => $roleEnum->description(),
                ]
            );
        }

        // Create branches
        $branches = [
            [
                'name' => 'portland',
                'display_name' => 'Portland',
                'address' => null,
                'phone' => null,
                'email' => null,
            ],
            [
                'name' => 'vancouver',
                'display_name' => 'Vancouver',
                'address' => null,
                'phone' => null,
                'email' => null,
            ],
        ];

        foreach ($branches as $branchData) {
            Branch::updateOrCreate(
                ['name' => $branchData['name']],
                $branchData
            );
        }

        // Migrate existing users from legacy role field to role_id
        $this->migrateLegacyRoles();
    }

    /**
     * Migrate existing users from legacy role field to role_id
     * Migrates all existing users to the new role system based on old 'role' field
     */
    private function migrateLegacyRoles(): void
    {
        $userRole = Role::where('name', UserRole::USER->value)->first();
        $adminRole = Role::where('name', UserRole::ADMIN->value)->first();

        if (!$userRole || !$adminRole) {
            $this->command->warn('Roles not found. Please run seeder again after roles are created.');
            return;
        }

        // Update users with role = null or 0 to user role
        $userCount = DB::table('users')
            ->whereNull('role_id')
            ->where(function ($query) {
                $query->whereNull('role')
                    ->orWhere('role', 0);
            })
            ->update(['role_id' => $userRole->id]);

        // Update users with role = 1 to admin role
        $adminCount = DB::table('users')
            ->whereNull('role_id')
            ->where('role', 1)
            ->update(['role_id' => $adminRole->id]);

        $this->command->info("Migrated {$userCount} users to 'user' role and {$adminCount} users to 'admin' role.");

        // Set default role for any remaining users without role_id
        $remainingCount = DB::table('users')
            ->whereNull('role_id')
            ->update(['role_id' => $userRole->id]);

        if ($remainingCount > 0) {
            $this->command->info("Set default 'user' role for {$remainingCount} remaining users.");
        }
    }
}
