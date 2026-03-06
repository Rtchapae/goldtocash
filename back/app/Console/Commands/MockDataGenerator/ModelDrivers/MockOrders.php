<?php

namespace App\Console\Commands\MockDataGenerator\ModelDrivers;

use App\Console\Commands\MockDataGenerator\MockDataGenerator;
use App\Models\Order;
use App\Models\User;

class MockOrders extends MockDataGenerator
{
    protected $signature = 'mock-orders {amount?}';

    public function handle()
    {
        $amount = $this->argument('amount') ?: 10;
        $users = [];
        $orders = [];

        while (count($users) < $amount) {
            $users[] = $this->makeUserData();
        }
        User::query()->insert($users);

        $maxUserId = User::query()->max('id') - $amount;

        while(count($orders) < $amount) {
            $maxUserId++;
            $orders[] = $this->makeOrderData($maxUserId);
        }

        Order::query()->insert($orders);
    }

    private function makeUserData(): array
    {
        return [
            'name' => $this->randomString(8),
            'phone' => $this->randomString(10, '1234567890'),
            'email' => $this->randomString(11),
            'password' => $this->randomString(12),
        ];
    }

    private function makeOrderData(int $id): array
    {
        return [
            'user_id' => $id,
            'status' => 0,
            'created_at' => now()->subHours($this->randomInt(2)),
        ];
    }
}
