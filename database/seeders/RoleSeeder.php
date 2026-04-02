<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['role_name' => 'user']);
        Role::create(['role_name' => 'administrator']);
        Role::create(['role_name' => 'super administrator']);
    }
}
