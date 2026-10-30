<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdvertisementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $slots = [
            [
                'slot_key' => 'header_leaderboard',
                'name' => 'Header Leaderboard (728x90)',
                'type' => 'image',
                'image_url' => null,
                'link_url' => null,
                'script_code' => null,
                'is_active' => false,
            ],
            [
                'slot_key' => 'sidebar_mrec',
                'name' => 'Sidebar MREC (300x250)',
                'type' => 'image',
                'image_url' => null,
                'link_url' => null,
                'script_code' => null,
                'is_active' => false,
            ],
            [
                'slot_key' => 'sidebar_skyscraper',
                'name' => 'Sidebar Skyscraper (300x600)',
                'type' => 'image',
                'image_url' => null,
                'link_url' => null,
                'script_code' => null,
                'is_active' => false,
            ],
            [
                'slot_key' => 'skin_ad_left',
                'name' => 'Skin Ad Left (160x600)',
                'type' => 'image',
                'image_url' => null,
                'link_url' => null,
                'script_code' => null,
                'is_active' => false,
            ],
            [
                'slot_key' => 'skin_ad_right',
                'name' => 'Skin Ad Right (160x600)',
                'type' => 'image',
                'image_url' => null,
                'link_url' => null,
                'script_code' => null,
                'is_active' => false,
            ],
            [
                'slot_key' => 'in_feed_leaderboard',
                'name' => 'In-Feed Leaderboard (728x90)',
                'type' => 'image',
                'image_url' => null,
                'link_url' => null,
                'script_code' => null,
                'is_active' => false,
            ],
            [
                'slot_key' => 'sponsor_leaderboard',
                'name' => 'Sponsor Leaderboard (728x90)',
                'type' => 'image',
                'image_url' => null,
                'link_url' => null,
                'script_code' => null,
                'is_active' => false,
            ],
            [
                'slot_key' => 'sidebar_sponsor_mrec',
                'name' => 'Sidebar Sponsor MREC (300x250)',
                'type' => 'image',
                'image_url' => null,
                'link_url' => null,
                'script_code' => null,
                'is_active' => false,
            ],
            [
                'slot_key' => 'bottom_billboard',
                'name' => 'Bottom Billboard (970x90)',
                'type' => 'image',
                'image_url' => null,
                'link_url' => null,
                'script_code' => null,
                'is_active' => false,
            ],
        ];

        foreach ($slots as $slot) {
            \App\Models\Advertisement::updateOrCreate(
                ['slot_key' => $slot['slot_key']],
                $slot
            );
        }
    }
}
