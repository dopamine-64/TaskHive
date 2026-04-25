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
            ['email' => 'admin@taskhive.com'], 
            [
                'name' => 'System Admin',
                'password' => Hash::make('admin1234'),
                'role' => 'admin',
                'latitude' => '23.8103',
                'longitude' => '90.4125',
            ]
        );
    }
}