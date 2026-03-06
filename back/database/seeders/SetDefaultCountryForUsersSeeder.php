<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Domain\Users\Models\User;

class SetDefaultCountryForUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Set country = 'USA' for all existing users that don't have a country set.
     */
    public function run(): void
    {
        $updated = User::query()
            ->where(function ($query) {
                $query->whereNull('country')
                    ->orWhere('country', '');
            })
            ->update(['country' => 'USA']);

        $this->command->info("Updated {$updated} users with country = 'USA'.");
    }
}
