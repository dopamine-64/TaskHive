<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@yourwebsite.com'], // The admin login email
            [
                'name' => 'System Admin',
                'password' => Hash::make('admin1234'), // The admin password
                'role' => 'admin',
                'latitude' => '23.8103', // Adding this since your DB requires it
                'longitude' => '90.4125',
            ]
        );
    }
}