<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Tạo tài khoản admin
        User::create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'full_name' => 'Administrator',
            'phone_number' => '0123456789',
            'address' => 'Địa chỉ admin',
            'role' => 'admin'
        ]);

        // Tạo 10 user thông thường
        User::factory()->count(10)->create();
    }
} 