<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define roles
        $admin = Role::create([
            'name' => 'SUPERADMIN',
            'guard_name' => 'web'
        ]);
        // $approver = Role::create(['name' => 'APPROVER']);
        // $editor = Role::create(['name' => 'EDITOR']);
    }
}
