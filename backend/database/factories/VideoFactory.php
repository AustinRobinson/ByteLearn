<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            's3_key' => 'videos/' . Str::uuid() . '.mp4', // Fake S3 path
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'likes' => 0,
            'is_banned' => false,
        ];
    }
}