<?php

namespace Database\Factories;

use Generator;
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
            's3_key' => $this->test_key_gen(),
            'title' => fake()->sentence(6),
            'description' => fake()->paragraph(5),
            'is_banned' => false,
        ];
    }

    private $test_keys = [
        's3://bytelearn-prod/videos/testing/Longest Consecutive Sequence - Leetcode 128 [ahysrRu0bHw].mp4',
        's3://bytelearn-prod/videos/testing/Permutations Leetcode 46 [aP3YATiM09g].mp4',
        's3://bytelearn-prod/videos/testing/Satisfying ascii animation with C - The doughnut shaped code that generates a spinning.mp4',
        's3://bytelearn-prod/videos/testing/Smiling_Seal_mQUKMX7aShI.mp4',
        's3://bytelearn-prod/videos/testing/the_BEST_IDE_for_programming_pt.2_a97QBk8QRtg.mp4',
        's3://bytelearn-prod/videos/testing/this_is_when_you_should_appreciate_abstraction_in_codingcoding_programming_javascript_python_OZ5TDCdjI78.mp4',
    ];

    private $curr_key = 0;

    public function test_key_gen(): string
    {
        $key_ret = $this->test_keys[$this->curr_key];
        $this->curr_key = ($this->curr_key + 1) % count($this->test_keys);
        return $key_ret;
    }

}
