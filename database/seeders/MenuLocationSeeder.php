<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('menu_locations')->insert([
            [
                'location_code' => 'header',
                'location_name' => 'Header',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'location_code' => 'quick_links',
                'location_name' => 'Quick Links',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'location_code' => 'information',
                'location_name' => 'Information',
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ]);
    }
}
