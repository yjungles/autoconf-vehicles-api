<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()
            ->withVehicles(3)
            ->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => 'password',
                'is_admin' => true,
            ]);

        User::factory()
            ->withVehicles(3)
            ->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
                'is_admin' => false,
            ]);

        User::factory(10)->withVehicles(3)->create();
    }
}
