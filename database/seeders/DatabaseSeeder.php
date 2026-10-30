<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([UserSeeder::class, CategorySeeder::class]);

        $user = User::where('email', 'superadmin@mail.com')->firstOrFail();

        $category = Category::firstOrCreate(['name' => 'Nasional', 'slug' => 'nasional']);

        $post = Post::factory()->create([
            'user_id' => $user->id,
            'title' => 'Selamat Datang di Kabar Sahabat',
            'slug' => 'selamat-datang-di-kabar-sahabat',
            'penulis' => 'Tim Redaksi',
            'editor' => 'Redaksi',
        ]);
        $post->categories()->attach($category->id);

        $this->call([
            AdvertisementSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
