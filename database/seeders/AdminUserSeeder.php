<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'last_name' => 'Admin',
            'first_name' => 'Super',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $admin->assignRole('admin');

        $user = User::create([
            'last_name' => 'User',
            'first_name' => 'Test',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->assignRole('user');
    }
}