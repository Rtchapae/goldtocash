<?php

namespace App\Console\Commands\MockDataGenerator\ModelDrivers;

use App\Console\Commands\MockDataGenerator\MockDataGenerator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MockLeads extends MockDataGenerator
{
    protected $signature = 'mock-admin';

    public function handle()
    {
        User::query()->updateOrCreate([
            'email' => 'hello@goldtocash.us',
        ], [
            'password' => Hash::make('test'),
            'role' => 1,
        ]);
    }
}
