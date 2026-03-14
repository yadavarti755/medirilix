<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FooterMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define Quick Links menu items
        $quickLinks = [
            [
                'location' => 'quick_links',
                'title'    => 'Privacy Policy',
                'url'      => '/privacy-policy',
                'parent_id' => null,
                'order'    => 1,
                'created_by' => 1,
                'updated_by' => 1
            ],
            [
                'location' => 'quick_links',
                'title'    => 'Return Policy',
                'url'      => '/return-policy',
                'parent_id' => null,
                'order'    => 2,
                'created_by' => 1,
                'updated_by' => 1
            ],
            [
                'location' => 'quick_links',
                'title'    => 'Refund & Cancellation Policy',
                'url'      => '/refund-cancellation-policy',
                'parent_id' => null,
                'order'    => 3,
                'created_by' => 1,
                'updated_by' => 1
            ],
            [
                'location' => 'quick_links',
                'title'    => 'Shipping Policy',
                'url'      => '/shipping-policy',
                'parent_id' => null,
                'order'    => 4,
                'created_by' => 1,
                'updated_by' => 1
            ],
            [
                'location' => 'quick_links',
                'title'    => 'Terms & Conditions',
                'url'      => '/terms-conditions',
                'parent_id' => null,
                'order'    => 5,
                'created_by' => 1,
                'updated_by' => 1
            ],
            [
                'location' => 'quick_links',
                'title'    => 'Disclaimer',
                'url'      => '/disclaimer',
                'parent_id' => null,
                'order'    => 6,
                'created_by' => 1,
                'updated_by' => 1
            ],
        ];

        // Define Information menu items
        $information = [
            [
                'location' => 'information',
                'title'    => 'Contact Us',
                'url'      => '/contact-us',
                'parent_id' => null,
                'order'    => 1,
                'created_by' => 1,
                'updated_by' => 1
            ],
            [
                'location' => 'information',
                'title'    => 'Login',
                'url'      => '/login',
                'parent_id' => null,
                'order'    => 2,
                'created_by' => 1,
                'updated_by' => 1
            ],
            [
                'location' => 'information',
                'title'    => 'Shop',
                'url'      => '/shop',
                'parent_id' => null,
                'order'    => 3,
                'created_by' => 1,
                'updated_by' => 1
            ],
            [
                'location' => 'information',
                'title'    => 'Homepage',
                'url'      => '/',
                'parent_id' => null,
                'order'    => 4,
                'created_by' => 1,
                'updated_by' => 1
            ],
        ];

        // Combine all menus
        $allMenus = array_merge($quickLinks, $information);

        // Insert menus
        foreach ($allMenus as $menu) {
            Menu::create($menu);
        }
    }
}
