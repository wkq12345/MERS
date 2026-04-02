<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::where('role_name', 'super administrator')->firstOrFail();
        $user = User::firstOrNew(['email' => 'superadmin@gmail.com']);
        $user->name = 'SuperAdmin';
        $user->password = bcrypt('password');
        $user->role_id = $superAdminRole->id;
        $user->save();
    }
}
