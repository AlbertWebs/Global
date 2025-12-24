<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles (or get existing)
        $superAdmin = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Full system access with all permissions',
            ]
        );

        $cashier = Role::firstOrCreate(
            ['slug' => 'cashier'],
            [
                'name' => 'Cashier',
                'description' => 'Can process payments and generate receipts',
            ]
        );

        // Create Permissions
        $permissions = [
            // Students
            ['name' => 'View Students', 'slug' => 'students.view', 'module' => 'students', 'action' => 'view'],
            ['name' => 'Create Students', 'slug' => 'students.create', 'module' => 'students', 'action' => 'create'],
            ['name' => 'Edit Students', 'slug' => 'students.edit', 'module' => 'students', 'action' => 'edit'],
            ['name' => 'Delete Students', 'slug' => 'students.delete', 'module' => 'students', 'action' => 'delete'],

            // Courses
            ['name' => 'View Courses', 'slug' => 'courses.view', 'module' => 'courses', 'action' => 'view'],
            ['name' => 'Create Courses', 'slug' => 'courses.create', 'module' => 'courses', 'action' => 'create'],
            ['name' => 'Edit Courses', 'slug' => 'courses.edit', 'module' => 'courses', 'action' => 'edit'],
            ['name' => 'Delete Courses', 'slug' => 'courses.delete', 'module' => 'courses', 'action' => 'delete'],
            ['name' => 'View Course Prices', 'slug' => 'courses.view_prices', 'module' => 'courses', 'action' => 'view_prices'],

            // Billing
            ['name' => 'Process Payments', 'slug' => 'billing.process', 'module' => 'billing', 'action' => 'process'],
            ['name' => 'View Payments', 'slug' => 'billing.view', 'module' => 'billing', 'action' => 'view'],
            ['name' => 'View Discounts', 'slug' => 'billing.view_discounts', 'module' => 'billing', 'action' => 'view_discounts'],

            // Receipts
            ['name' => 'Generate Receipts', 'slug' => 'receipts.generate', 'module' => 'receipts', 'action' => 'generate'],
            ['name' => 'View Receipts', 'slug' => 'receipts.view', 'module' => 'receipts', 'action' => 'view'],
            ['name' => 'Print Receipts', 'slug' => 'receipts.print', 'module' => 'receipts', 'action' => 'print'],

            // Expenses
            ['name' => 'View Expenses', 'slug' => 'expenses.view', 'module' => 'expenses', 'action' => 'view'],
            ['name' => 'Create Expenses', 'slug' => 'expenses.create', 'module' => 'expenses', 'action' => 'create'],
            ['name' => 'Edit Expenses', 'slug' => 'expenses.edit', 'module' => 'expenses', 'action' => 'edit'],
            ['name' => 'Delete Expenses', 'slug' => 'expenses.delete', 'module' => 'expenses', 'action' => 'delete'],

            // Reports
            ['name' => 'View Reports', 'slug' => 'reports.view', 'module' => 'reports', 'action' => 'view'],

            // Users & Roles
            ['name' => 'View Users', 'slug' => 'users.view', 'module' => 'users', 'action' => 'view'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'module' => 'users', 'action' => 'create'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'module' => 'users', 'action' => 'edit'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'module' => 'users', 'action' => 'delete'],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'module' => 'roles', 'action' => 'manage'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['slug' => $perm['slug']],
                $perm
            );
        }

        // Assign all permissions to Super Admin
        $superAdmin->permissions()->attach(Permission::all()->pluck('id'));

        // Assign limited permissions to Cashier
        $cashierPermissions = Permission::whereIn('slug', [
            'students.view',
            'courses.view',
            'billing.process',
            'billing.view',
            'receipts.generate',
            'receipts.view',
            'receipts.print',
            'expenses.view',
            'expenses.create',
            'expenses.edit',
        ])->pluck('id');

        $cashier->permissions()->attach($cashierPermissions);
    }
}
