<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class FinancePermsSeeder extends Seeder
{
    public function run()
    {
        $role = Role::where('key', 'finance')->first();
        
        if (!$role) {
            $this->command->error("Finance role not found!");
            return;
        }

        $permissions = [
            // Payrolls
            'payrolls.view', 'payrolls.create', 'payrolls.edit', 'payrolls.delete',
            'payroll.view', 'payroll.create', 'payroll.edit', 'payroll.delete',
            
            // Employee Tasks
            'employee-tasks.view', 'employee-tasks.create', 'employee-tasks.edit', 'employee-tasks.delete',
            'employee_tasks.view', 'employee_tasks.create', 'employee_tasks.edit', 'employee_tasks.delete',
            'tasks.view', // Just in case
            
            // Allow finance to view basic lists needed for dropdowns
            'users.view',
        ];

        foreach ($permissions as $key) {
            $permission = Permission::firstOrCreate(
                ['key' => $key],
                ['name' => ucwords(str_replace(['.', '_', '-'], ' ', $key))]
            );
            
            if (!$role->permissions->contains($permission->id)) {
                $role->permissions()->attach($permission->id);
                $this->command->info("Granted $key to finance.");
            }
        }
    }
}
