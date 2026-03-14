<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalutationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('salutations')->insert([
            [
                'order' => 1,
                'name' => 'Mr.',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'order' => 2,
                'name' => 'Mrs.',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'order' => 3,
                'name' => 'Ms.',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'order' => 4,
                'name' => 'MX.',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'order' => 5,
                'name' => 'Rather not say',
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ]);
    }
}
