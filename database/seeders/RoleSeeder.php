<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert multiple roles correctly
        Role::insert([
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'employee', 'guard_name' => 'web'],
            ['name' => 'accountant', 'guard_name' => 'web'],
        ]);

        // Retrieve roles
        $adminRole = Role::where('name', 'admin')->first();
        $employeeRole = Role::where('name', 'employee')->first();
        $accountantRole = Role::where('name', 'accountant')->first();
        
        // Get all permissions
        $allPermissions = Permission::pluck('name');

        // Assign ALL permissions to the 'admin' role
        $adminRole->syncPermissions($allPermissions);

        // Assign limited permissions to 'employee'
        $employeeRole->syncPermissions([
            'leave_create',
            'leave_delete',
        ]);
        
        // Assign permissions to 'accountant'
        $accountantRole->syncPermissions([
            'invoices_create',
            'invoices_edit',
            'invoices_pay',
            'balance_create',
        ]);
    }
}
