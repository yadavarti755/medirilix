<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SiteSetting::insert([
            'site_name' => 'Medirilix',
            'site_tag_line' => 'Best product supply',
            'seo_keywords' => 'Medirilix',
            'seo_description' => 'Medirilix',
            'header_logo' => 'logo.png',
            'footer_logo' => 'logo.png',
            'favicon' => 'favicon.ico',
            'copyright_text' => 'All rights reserved',
            'maintained_by_text' => 'Maintained by Medirilix',
            'accessibility_text' => 'Website is accessible on all browsers',
        ]);
    }
}
