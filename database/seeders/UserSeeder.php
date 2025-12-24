<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles if they don't exist
        $superAdminRole = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Full system access with all permissions',
            ]
        );

        $cashierRole = Role::firstOrCreate(
            ['slug' => 'cashier'],
            [
                'name' => 'Cashier',
                'description' => 'Can process payments and generate receipts',
            ]
        );

        // Create users if they don't exist
        User::firstOrCreate(
            ['email' => 'admin@globalcollege.edu'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role_id' => $superAdminRole->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'cashier@globalcollege.edu'],
            [
                'name' => 'Cashier',
                'password' => Hash::make('password'),
                'role_id' => $cashierRole->id,
            ]
        );
    }
}
