<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Post;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'category' => $this->faker->randomElement(['Technology', 'Lifestyle', 'Education']),
            'author_id' => \App\Models\User::factory(), // Create a user for each post
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
