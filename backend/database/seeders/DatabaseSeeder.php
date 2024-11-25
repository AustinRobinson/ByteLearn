<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tag;
use App\Models\Video;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tags = Tag::factory()->count(8)->create();
        $users = User::factory()->count(3)->unverified()->create();

        foreach ($users as $user) {
            $user->tags()->attach(
                $tags->random(rand(2, 4))->pluck('id')->toArray()
            );


            // Create 2-4 videos for each user
            Video::factory()
                ->count(rand(2, 3))
                ->create([
                    'user_id' => $user->id
                ])
                ->each(function ($video) use ($tags) {
                    // Attach 1-3 random tags to each video
                    $video->tags()->attach(
                        $tags->random(rand(1, 3))->pluck('id')->toArray()
                    );
                });
        }
    }
}
