<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class SystemUsersSeeder extends Seeder
{
    public function run()
    {
        $usersRequired = [
            [
                'email' => 'admin@ensan.local',
                'name' => 'مدير المؤسسة بالكامل',
                'role_key' => 'admin',
                'role_name' => 'مدير المؤسسة بالكامل'
            ],
            [
                'email' => 'manager@ensan.local',
                'name' => 'إدارة',
                'role_key' => 'manager',
                'role_name' => 'إدارة'
            ],
            [
                'email' => 'project_manager@ensan.local',
                'name' => 'مدير المشاريع',
                'role_key' => 'project_manager',
                'role_name' => 'مدراء المشاريع'
            ],
            [
                'email' => 'reception@ensan.local',
                'name' => 'موظف الاستقبال',
                'role_key' => 'receptionist',
                'role_name' => 'الاستقبال'
            ],
            [
                'email' => 'finance@ensan.local',
                'name' => 'موظف الحسابات',
                'role_key' => 'finance',
                'role_name' => 'الحسابات'
            ],
            [
                'email' => 'hr@ensan.local',
                'name' => 'مدير الموارد البشرية HR',
                'role_key' => 'hr',
                'role_name' => 'الموارد البشرية'
            ],
            [
                'email' => 'guest_house_manager@ensan.local',
                'name' => 'مدير دار الضيافة',
                'role_key' => 'guest_house_manager',
                'role_name' => 'مدراء دار الضيافه'
            ]
        ];

        foreach ($usersRequired as $u) {
            $role = Role::firstOrCreate(
            ['key' => $u['role_key']],
            ['name' => $u['role_name']]
            );

            $user = User::firstOrCreate(
            ['email' => $u['email']],
            [
                'name' => $u['name'],
                'password' => Hash::make('12345678'),
                'is_employee' => true,
                'active' => true,
                'job_title' => $u['name'],
            ]
            );

            if (!$user->roles->contains($role->id)) {
                $user->roles()->attach($role->id);
            }
        }
    }
}
