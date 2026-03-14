<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles exist
        $superAdminRole = Role::where('name', 'SUPERADMIN')->first();

        // Create Super Admin User
        $superAdmin = User::create([
            'name' => 'Super Admin User',
            'email' => 'superadmin@gmail.com',
            'mobile_number' => '9560314444',
            'password' => Hash::make('password'),
            'created_by' => 1,
            'updated_by' => 1,
        ]);
        $superAdmin->assignRole($superAdminRole);
    }
}
