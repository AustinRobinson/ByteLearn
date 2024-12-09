<?php

namespace Tests\Feature\Video;

use App\Models\User;
use App\Models\Video;
use App\Models\Comment;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoCommentTest extends TestCase
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

    public function test_can_get_video_comments(): void
    {
        // Let's create some test comments to ensure the get comments functionality works as expected
        $comment = Comment::factory()
            ->for($this->video)
            ->for($this->user)
            ->create();

        $reply = Comment::factory()
            ->for($this->video)
            ->for($this->user)
            ->create(['comment_id' => $comment->id]);

        // Now we can make a request to the endpoint that retrieves comments for the video
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->getJson(
            "/api/video-comments/{$this->video->id}"
        );

        // Assert that the response has the expected structure, including the comment and reply data
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'text',
                        'is_user_creator',
                        'user' => [
                            'id',
                            'username'
                        ],
                        'replies' => [
                            '*' => [
                                'id',
                                'text',
                                'is_user_creator',
                                'user' => [
                                    'id',
                                    'username'
                                ]
                            ]
                        ]
                    ]
                ],
                'meta' => [
                    'total_comments'
                ]
            ]);
    }

    public function test_can_create_comment(): void
    {
        // In this test, we'll ensure that the user can create a new comment for the video
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->postJson(
            "/api/video-comments/{$this->video->id}",
            ['comment' => 'Test comment']
        );

        // Assert that the comment was created successfully
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'text',
                    'user' => [
                        'id',
                        'username'
                    ]
                ]
            ]);

        // Verify that the comment was actually saved to the database
        $this->assertDatabaseHas('comments', [
            'video_id' => $this->video->id,
            'user_id' => $this->user->id,
            'comment' => 'Test comment'
        ]);
    }

    public function test_can_reply_to_comment(): void
    {
        // First, we need to create a parent comment that the reply can be associated with
        $parentComment = Comment::factory()
            ->for($this->video)
            ->for($this->user)
            ->create();

        // Now we can make a request to create a reply to the parent comment
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->postJson(
            "/api/video-comments/{$this->video->id}",
            [
                'comment' => 'Test reply',
                'comment_id' => $parentComment->id
            ]
        );

        // Assert that the reply was created successfully
        $response->assertStatus(201);

        // Verify that the reply was saved to the database with the expected parent comment ID
        $this->assertDatabaseHas('comments', [
            'video_id' => $this->video->id,
            'user_id' => $this->user->id,
            'comment' => 'Test reply',
            'comment_id' => $parentComment->id
        ]);
    }

    public function test_cannot_reply_to_nonexistent_comment(): void
    {
        // In this test, we'll attempt to create a reply to a comment that doesn't exist
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->postJson(
            "/api/video-comments/{$this->video->id}",
            [
                'comment' => 'Test reply',
                'comment_id' => null
            ]
        );

        // We expect the response to have a 422 Unprocessable Entity status code and include a validation error for the comment_id field
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['comment_id']);
    }

    public function test_can_toggle_comment_like(): void
    {
        // First, we'll create a comment to test the like/unlike functionality
        $comment = Comment::factory()
            ->for($this->video)
            ->for($this->user)
            ->create();

        // Now we can make a request to like the comment
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->postJson(
            "/api/comments/{$comment->id}/like"
        );

        // Assert that the like was successful
        $response->assertStatus(200)
            ->assertJson(['message' => 'Comment liked successfully']);

        // Next, we'll make a request to unlike the comment
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->postJson(
            "/api/comments/{$comment->id}/like"
        );

        // Assert that the unlike was successful
        $response->assertStatus(200)
            ->assertJson(['message' => 'Comment unliked successfully']);
    }
}