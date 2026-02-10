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

            // Course Registrations
            ['name' => 'View Course Registrations', 'slug' => 'course_registrations.view', 'module' => 'course_registrations', 'action' => 'view'],
            ['name' => 'Create Course Registrations', 'slug' => 'course_registrations.create', 'module' => 'course_registrations', 'action' => 'create'],
            ['name' => 'Delete Course Registrations', 'slug' => 'course_registrations.delete', 'module' => 'course_registrations', 'action' => 'delete'],

            // Bank Deposits
            ['name' => 'View Bank Deposits', 'slug' => 'bank_deposits.view', 'module' => 'bank_deposits', 'action' => 'view'],
            ['name' => 'Create Bank Deposits', 'slug' => 'bank_deposits.create', 'module' => 'bank_deposits', 'action' => 'create'],
            ['name' => 'Edit Bank Deposits', 'slug' => 'bank_deposits.edit', 'module' => 'bank_deposits', 'action' => 'edit'],
            ['name' => 'Delete Bank Deposits', 'slug' => 'bank_deposits.delete', 'module' => 'bank_deposits', 'action' => 'delete'],

            // Other Income
            ['name' => 'View Other Income', 'slug' => 'other_income.view', 'module' => 'other_income', 'action' => 'view'],
            ['name' => 'Create Other Income', 'slug' => 'other_income.create', 'module' => 'other_income', 'action' => 'create'],
            ['name' => 'Edit Other Income', 'slug' => 'other_income.edit', 'module' => 'other_income', 'action' => 'edit'],
            ['name' => 'Delete Other Income', 'slug' => 'other_income.delete', 'module' => 'other_income', 'action' => 'delete'],

            // Income Statement
            ['name' => 'View Income Statement', 'slug' => 'income_statement.view', 'module' => 'income_statement', 'action' => 'view'],

            // Balances
            ['name' => 'View Balances', 'slug' => 'balances.view', 'module' => 'balances', 'action' => 'view'],
            ['name' => 'Edit Balances', 'slug' => 'balances.edit', 'module' => 'balances', 'action' => 'edit'],

            // Payment Logs
            ['name' => 'View Payment Logs', 'slug' => 'payment_logs.view', 'module' => 'payment_logs', 'action' => 'view'],

            // Teachers
            ['name' => 'View Teachers', 'slug' => 'teachers.view', 'module' => 'teachers', 'action' => 'view'],
            ['name' => 'Create Teachers', 'slug' => 'teachers.create', 'module' => 'teachers', 'action' => 'create'],
            ['name' => 'Edit Teachers', 'slug' => 'teachers.edit', 'module' => 'teachers', 'action' => 'edit'],
            ['name' => 'Delete Teachers', 'slug' => 'teachers.delete', 'module' => 'teachers', 'action' => 'delete'],

            // Settings
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'module' => 'settings', 'action' => 'manage'],

            // Bulk SMS
            ['name' => 'Send Bulk SMS', 'slug' => 'bulk_sms.send', 'module' => 'bulk_sms', 'action' => 'send'],

            // Data Purge
            ['name' => 'Data Purge', 'slug' => 'data_purge.manage', 'module' => 'data_purge', 'action' => 'manage'],

            // Money Trace
            ['name' => 'View Money Trace', 'slug' => 'money_trace.view', 'module' => 'money_trace', 'action' => 'view'],

            // Fee Balances
            ['name' => 'View Fee Balances', 'slug' => 'fee_balances.view', 'module' => 'fee_balances', 'action' => 'view'],
            ['name' => 'Send Fee Reminders', 'slug' => 'fee_balances.send_reminders', 'module' => 'fee_balances', 'action' => 'send_reminders'],

            // Mobile Dashboard
            ['name' => 'View Mobile Dashboard', 'slug' => 'mobile_dashboard.view', 'module' => 'mobile_dashboard', 'action' => 'view'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['slug' => $perm['slug']],
                $perm
            );
        }

        // Assign all permissions to Super Admin (use sync to avoid duplicates)
        $superAdmin->permissions()->sync(Permission::all()->pluck('id'));

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
            'bank_deposits.view',
            'bank_deposits.create',
            'bank_deposits.edit',
            'other_income.view',
            'other_income.create',
            'other_income.edit',
            'course_registrations.view',
            'course_registrations.create',
        ])->pluck('id');

        $cashier->permissions()->sync($cashierPermissions);
    }
}
