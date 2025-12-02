<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            "address_create", "address_delete", "address_edit", "address_update",
            "balance_create",
            "client_create", "client_delete", "client_edit", "client_update",
            "company_create", "company_delete", "company_edit", "company_update",
            "create_roles", "create_users",
            "delete_roles", "delete_users",
            "expense_create", "expense_delete", "expense_edit", "expense_update",
            "invoices_create", "invoices_edit", "invoices_pay",
            "leave_create", "leave_delete", "leave_edit", "leave_update",
            "permission_create", "permission_delete", "permission_edit", "permission_update",
            "quotation_create", "quotation_delete", "quotation_edit", "send_quotation",
            "task_create", "task_delete", "task_edit", "task_update",
            "update_roles", "update_users",
            "view_roles", "view_users",
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
