<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class AuthAndCommentTest extends TestCase
{
    use RefreshDatabase; // Ensures DB is reset after each test

    protected $user;
    protected $headers;

    public function setUp(): void
    {
        parent::setUp();

        // Create a test user for login
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);
    }

    //TEST CREATE COMMENT SUCCESSFULLY
    public function test_user_can_create_comment()
    {
        // Login user to get token
        $token = auth()->login($this->user);

        // Create a post
        $post = Post::factory()->create();

        $data = ['content' => 'This is a great post!'];

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->postJson("/api/posts/{$post->id}/comments", $data);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Comment added successfully']);

        $this->assertDatabaseHas('comments', ['content' => 'This is a great post!']);
    }

    //TEST COMMENT FAILS WITHOUT CONTENT
    public function test_comment_fails_without_content()
    {
        $token = auth()->login($this->user);
        $post = Post::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->postJson("/api/posts/{$post->id}/comments", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content']);
    }
}
