<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrCreate(
            ['id' => 1],
            [
                'tagline' => 'Jernih Melihat Pergerakan',
                'copyright' => 'Copyright &copy; ' . now()->format('Y') . ' Kabar Sahabat. All Rights Reserved',
                'instagram_url' => 'https://instagram.com/kabarsahabat',
                'twitter_url' => 'https://twitter.com/kabarsahabat',
                'youtube_url' => 'https://youtube.com/kabarsahabat',
                'email' => 'redaksi@kabarsahabat.id',
                'phone' => '081234567890',
                'address' => 'Semarang, Jawa Tengah, Indonesia',
                'analytics_code' => '<!-- Default Analytics Tracking Code -->',
            ]
        );
    }
}
