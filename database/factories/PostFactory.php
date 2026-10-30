<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(6);

        return [
            'user_id' => 1, // Assuming superadmin is ID 1
            'title' => $title,
            'slug' => str()->slug($title),
            // Set null so Filament FileUpload doesn't try to resolve an external URL as a local file path
            'image_url' => null,
            'excerpt' => $this->faker->paragraph(2),
            // A realistic HTML content mockup
            'content' => '<p>'.implode('</p><p>', $this->faker->paragraphs(5)).'</p><h2>Subjudul Menarik</h2><p>'.implode('</p><p>', $this->faker->paragraphs(3)).'</p>',
            'status' => 'Published',
            'views_count' => $this->faker->numberBetween(100, 5000),
            'published_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
