<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class CustomUserSeeder extends Seeder
{
    public function run()
    {
        $user = User::firstOrCreate(
            ['email' => 'IbrahimElfil@gmail.com'],
            [
                'name' => 'Ibrahim',
                'password' => Hash::make('IbrahimElfil'),
                'is_employee' => true,
                'active' => true,
                'job_title' => 'Admin',
            ]
        );

        $role = Role::where('key', 'admin')->first();
        if ($role && !$user->roles->contains($role->id)) {
            $user->roles()->attach($role->id);
        }
    }
}
