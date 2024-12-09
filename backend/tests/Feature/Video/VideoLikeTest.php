<?php

namespace Tests\Feature\Video;

use App\Models\User;
use App\Models\Video;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoLikeTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $video;
    private $accessToken;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('scout.driver', null);

        // Create test data
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        // Simulate user login and get access token
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);
        $this->accessToken = $response->json('access_token');

        // Create a test video
        $this->video = Video::factory()
            ->for($this->user)
            ->create(['is_banned' => false]);
    }

    public function test_user_can_like_video(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->postJson(
            "/api/video-like/{$this->video->id}"
        );

        $response->assertStatus(200)
            ->assertJson(['message' => 'Video liked successfully']);

        $this->assertDatabaseHas('user_video_like', [
            'user_id' => $this->user->id,
            'video_id' => $this->video->id
        ]);
    }

    public function test_user_can_unlike_video(): void
    {
        // Pre-like the video
        $this->video->likedBy()->attach($this->user->id);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->deleteJson(
            "/api/video-like/{$this->video->id}"
        );

        $response->assertStatus(200)
            ->assertJson(['message' => 'Video unliked successfully']);

        $this->assertDatabaseMissing('user_video_like', [
            'user_id' => $this->user->id,
            'video_id' => $this->video->id
        ]);
    }

    public function test_video_like_is_unique(): void
    {
        // Try to like the same video twice
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->postJson(
            "/api/video-like/{$this->video->id}"
        );

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->postJson(
            "/api/video-like/{$this->video->id}"
        );

        $response->assertStatus(200)
            ->assertJson(['message' => 'Video liked successfully']);

        // Should only have one like record
        $this->assertEquals(
            1,
            $this->video->likedBy()->where('user_id', $this->user->id)->count()
        );
    }

    public function test_cannot_like_nonexistent_video(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->postJson(
            "/api/video-like/nonexistent-id"
        );

        $response->assertStatus(404);
    }
}