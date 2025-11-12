<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin User', 'password' => Hash::make('password')]
        );
        $admin->assignRole('admin');

        // Create Staff
        $staff = User::firstOrCreate(
            ['email' => 'staff@example.com'],
            ['name' => 'Staff User', 'password' => Hash::make('password')]
        );
        $staff->assignRole('staff');

        // Create Regular User
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            ['name' => 'Regular User', 'password' => Hash::make('password')]
        );
        $user->assignRole('user');
    
    }
}
