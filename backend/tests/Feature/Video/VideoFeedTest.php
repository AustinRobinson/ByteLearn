<?php

namespace Tests\Feature\Video;

use App\Models\User;
use App\Models\Video;
use App\Models\Tag;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoFeedTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $accessToken;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('scout.driver', null);

        $this->seed();

        $this->user = User::first();

        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'password'
        ]);

        $this->accessToken = $response->json('access_token');
    }

    private function cleanupVideos(): void 
    {
        DB::table('video_tag')->delete();
        DB::table('user_video_like')->delete();
        DB::table('user_watched_video')->delete();
        DB::table('playlist_video')->delete();
        DB::table('comments')->delete();
        Video::query()->delete();
    }

    public function test_feed_returns_videos_with_proper_structure(): void
    {
        $this->cleanupVideos();
        
        Video::factory()
            ->count(3)
            ->for($this->user)
            ->create(['is_banned' => false]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->getJson('/api/videos/feed');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        's3_key',
                        'created_at',
                        'like_count',
                        'user' => [
                            'id',
                            'username'
                        ],
                        'tags',
                        'is_liked',
                        'has_watched',
                        'comment_count',
                        'matches_interests'
                    ]
                ],
                'meta' => [
                    'total',
                    'offset',
                    'limit',
                    'has_interests'
                ]
            ]);
    }

    public function test_feed_respects_pagination(): void
    {
        $this->cleanupVideos();
        
        Video::factory()
            ->count(25)
            ->for($this->user)
            ->create(['is_banned' => false]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->getJson('/api/videos/feed?limit=10&offset=5');

        $response->assertStatus(200)
            ->assertJsonPath('meta.limit', '10')
            ->assertJsonPath('meta.offset', '5')
            ->assertJsonCount(10, 'data');
    }

    public function test_feed_prioritizes_videos_matching_user_interests(): void
    {
        $this->cleanupVideos();
        
        $tags = Tag::factory()->count(2)->create();
        $this->user->interests()->attach($tags->pluck('id'));

        $matchingVideo = Video::factory()
            ->for($this->user)
            ->create(['is_banned' => false]);
        $matchingVideo->tags()->attach($tags->first()->id);

        $nonMatchingVideo = Video::factory()
            ->for($this->user)
            ->create(['is_banned' => false]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->getJson('/api/videos/feed');

        $response->assertStatus(200);
        
        $videos = $response->json('data');
        $this->assertTrue($videos[0]['matches_interests']);
        $this->assertEquals($matchingVideo->id, $videos[0]['id']);
    }

    public function test_feed_excludes_banned_videos(): void
    {
        $this->cleanupVideos();
        
        $bannedVideo = Video::factory()
            ->for($this->user)
            ->create(['is_banned' => true]);
            
        $normalVideo = Video::factory()
            ->for($this->user)
            ->create(['is_banned' => false]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->getJson('/api/videos/feed');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');

        $videos = collect($response->json('data'));
        $this->assertFalse($videos->pluck('id')->contains($bannedVideo->id));
        $this->assertTrue($videos->pluck('id')->contains($normalVideo->id));
    }
}