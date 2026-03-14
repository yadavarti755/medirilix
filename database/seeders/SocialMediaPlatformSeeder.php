<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocialMediaPlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $platforms = [
            ['name' => 'Facebook'],
            ['name' => 'Twitter'],
            ['name' => 'Instagram'],
            ['name' => 'LinkedIn'],
            ['name' => 'YouTube'],
            ['name' => 'TikTok'],
            ['name' => 'Snapchat'],
            ['name' => 'Pinterest'],
        ];

        $now = Carbon::now();

        foreach ($platforms as &$platform) {
            $platform['created_at'] = $now;
            $platform['updated_at'] = $now;
        }

        DB::table('social_media_platforms')->insert($platforms);
    }
}
