<?php

namespace Database\Factories;

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
            's3_key' => fake()->md5(),
            'title' => fake()->sentence(6),
            'description' => fake()->paragraph(5),
            'is_banned' => false,
        ];
    }
}
