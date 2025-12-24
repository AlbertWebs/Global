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
        $superAdminRole = Role::where('slug', 'super-admin')->first();

        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@globalcollege.edu',
            'password' => Hash::make('password'),
            'role_id' => $superAdminRole->id,
        ]);

        $cashierRole = Role::where('slug', 'cashier')->first();

        User::create([
            'name' => 'Cashier',
            'email' => 'cashier@globalcollege.edu',
            'password' => Hash::make('password'),
            'role_id' => $cashierRole->id,
        ]);
    }
}
