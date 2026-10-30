<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Seed kanal berita sesuai copywriting Kabar Sahabat.
     */
    public function run(): void
    {
        $kanal = [
            'Berita',
            'Opini',
            'Tokoh',
            'Pendidikan & Budaya',
            'Politik & Kebangsaan',
            'Hukum & Masyarakat',
            'Ekonomi',
            'Teknologi & Digital',
            'Religi',
            'Gaya Hidup & Kreativitas',
        ];

        foreach ($kanal as $name) {
            Category::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}
