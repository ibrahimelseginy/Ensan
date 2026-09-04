<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MarketerRoleSeeder extends Seeder
{
    public function run()
    {
        // 1. Create the Marketer Role
        $role = Role::firstOrCreate(
            ['key' => 'marketer'],
            ['name' => 'المسؤول التسويقي (Marketer)']
        );

        // 2. Define permissions for this role
        $permissions = [
            'dashboard.view',
            'website.view',
            'website.create',
            'website.edit',
            'website.delete',
            'mobile.view',
            'mobile.create',
            'mobile.edit',
            'mobile.delete',
            'campaigns.view',
            'projects.view',
        ];

        // 3. Attach permissions to role
        foreach ($permissions as $key) {
            $permission = Permission::where('key', $key)->first();
            if ($permission && !$role->permissions->contains($permission->id)) {
                $role->permissions()->attach($permission->id);
            }
        }

        // 4. Create a demo marketer user if not exists
        $user = User::firstOrCreate(
            ['email' => 'marketer@ensan.local'],
            [
                'name' => 'مسؤول التسويق',
                'password' => Hash::make('password'),
                'is_employee' => true,
                'active' => true,
                'salary' => 6000,
                'join_date' => now(),
            ]
        );

        if (!$user->roles->contains($role->id)) {
            $user->roles()->attach($role->id);
        }
    }
}
