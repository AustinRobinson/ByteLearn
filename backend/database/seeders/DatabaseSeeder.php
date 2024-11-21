<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tag;
use App\Models\Video;
use App\Models\Comment;
use App\Models\Playlist;
use App\Models\UserStrike;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // creating initial tags, users, and videos
        $tags = Tag::factory()->count(8)->create();
        $users = User::factory()
            ->count(3)
            ->has(
                Video::factory()->count(3)
            )
            ->has(Playlist::factory())
            ->unverified()
            ->create();

        // create comments for each video
        foreach(Video::all() as $vid) {
            $commenter = User::factory()->unverified()->create();
            $comments = Comment::factory()
                ->count(3)
                ->for($vid)
                ->for($commenter)
                ->create();
        }

        // attach tags to users to represent user interests
        foreach ($users as $user) {
            $user->tags()->attach(
                $tags->random(rand(2, 4))->pluck('id')->toArray()
            );
        }

        // set comment replies to other comments within the last video
        $video = Video::all()->last();
        $comments = Comment::where('video_id', $video->id)->get();
        $comments->get(1)->parentComment()->associate($comments->get(0));
        $comments->get(1)->save();
        $comments->get(1)->replies()->save($comments->get(2));

        // create a new follower who follows the first user
        $follower = User::factory()->unverified()->create();
        $follower->usersFollows()->attach($users[0]->id, ['followed_at' => now()]);

        // create a new creator who is followed by the third user in the above
        // users collection
        $followed = User::factory()->unverified()->create();
        $followed->usersFollowedBy()->attach($users->last()->id, ['followed_at' => now()]);

        // create a user with a strike
        User::factory()
            ->has(
                UserStrike::factory()
            )
            ->unverified()
            ->create();

        // put videos in a playlist
        $playlist = Playlist::all()->first();
        $videosForPlaylist = Video::all()->take(4);
        foreach ($videosForPlaylist as $key=>$vid) {
            $vid->playlists()->attach($playlist->id, [
                'added_at' => now(),
                'order' => $key,
            ]);
        }
    }
}
