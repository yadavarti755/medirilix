<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define main menu items
        $menus = [
            [
                'location' => 'header',
                'title'    => 'Home',
                'url'      => '/',
                'parent_id' => null,
                'order'    => 1,
                'created_by' => 1,
                'updated_by' => 1
            ],
            [
                'location' => 'header',
                'title'    => 'Our Shop',
                'url'      => '/shop',
                'parent_id' => null,
                'order'    => 3,
                'created_by' => 1,
                'updated_by' => 1
            ],
            [
                'location' => 'header',
                'title'    => 'Our Categories',
                'url'      => '#',
                'parent_id' => null,
                'order'    => 4,
                'created_by' => 1,
                'updated_by' => 1
            ],
        ];

        // Insert menus
        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}
