<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tag;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // creating initial tags and users
        $tags = Tag::factory()->count(8)->create();
        $users = User::factory()->count(3)->unverified()->create();

        // attach tags to users to represent user interests
        foreach ($users as $user) {
            $user->tags()->attach(
                $tags->random(rand(2, 4))->pluck('id')->toArray()
            );
        }

        // create a new follower who follows the first user
        $follower = User::factory()->unverified()->create();
        $follower->usersFollows()->attach($users[0]->id, ['followed_at' => now()]);

        // create a new creator who is followed by the third user in the above
        // users collection
        $followed = User::factory()->unverified()->create();
        $followed->usersFollowedBy()->attach($users->last()->id, ['followed_at' => now()]);
    }
}
