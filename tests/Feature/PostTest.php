<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;

class PostTest extends TestCase
{
    use RefreshDatabase; // Resets DB after each test

    protected $user;
    protected $headers;

    public function setUp(): void
    {
        parent::setUp();

        // Create a user and authenticate
        $this->user = User::factory()->create();
        $token = auth()->login($this->user);
        $this->headers = ['Authorization' => "Bearer $token"];
    }

    //TEST CREATE A POST SUCCESSFULLY
    public function test_user_can_create_a_post()
    {
        $data = [
            'title' => 'New Post',
            'content' => 'This is a test post.',
            'category' => 'Technology'
        ];

        $response = $this->withHeaders($this->headers)
            ->postJson('/api/posts', $data);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Post created successfully']);

        $this->assertDatabaseHas('posts', ['title' => 'New Post']);
    }

    //TEST CREATE POST FAILS WITH MISSING FIELDS
    public function test_post_creation_fails_with_missing_fields()
    {
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/posts', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content', 'category']);
    }

    //TEST GET ALL POSTS WITH PAGINATION
    public function test_get_all_posts()
    {
        Post::factory(5)->create();

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    //TEST GET SINGLE POST SUCCESSFULLY
    public function test_get_single_post()
    {
        $post = Post::factory()->create();

        $response = $this->getJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJson(['id' => $post->id]);
    }

    //TEST GET SINGLE POST FAILS (NOT FOUND)
    public function test_get_single_post_fails_if_not_found()
    {
        $response = $this->getJson("/api/posts/999");

        $response->assertStatus(404)
            ->assertJson(['error' => 'Resource not found']);
    }

    //TEST UPDATE POST SUCCESSFULLY
    public function test_user_can_update_a_post()
    {
        $post = Post::factory()->create(['author_id' => $this->user->id]);

        $updateData = ['title' => 'Updated Post Title'];

        $response = $this->withHeaders($this->headers)
            ->putJson("/api/posts/{$post->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Post updated successfully']);

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Updated Post Title']);
    }

    //TEST UPDATE FAILS FOR UNAUTHORIZED USER
    public function test_update_fails_for_unauthorized_user()
    {
        $post = Post::factory()->create();

        $updateData = ['title' => 'Hacked Post Title'];

        $response = $this->withHeaders($this->headers)
            ->putJson("/api/posts/{$post->id}", $updateData);

        $response->assertStatus(403);
    }

    //TEST DELETE POST SUCCESSFULLY
    public function test_user_can_delete_a_post()
    {
        $post = Post::factory()->create(['author_id' => $this->user->id]);

        $response = $this->withHeaders($this->headers)
            ->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Post deleted successfully']);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    //TEST DELETE FAILS FOR UNAUTHORIZED USER
    public function test_delete_fails_for_unauthorized_user()
    {
        $post = Post::factory()->create();

        $response = $this->withHeaders($this->headers)
            ->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(403);
    }

    //TEST FILTER POSTS BY CATEGORY
    public function test_filter_posts_by_category()
    {
        Post::factory()->create(['category' => 'Technology']);
        Post::factory()->create(['category' => 'Lifestyle']);

        $response = $this->getJson('/api/posts?category=Technology');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    //TEST FILTER POSTS BY DATE RANGE
    public function test_filter_posts_by_date_range()
    {
        Post::factory()->create(['created_at' => now()->subDays(5)]);
        Post::factory()->create(['created_at' => now()->subDays(2)]);

        $response = $this->getJson('/api/posts?start_date=' . now()->subDays(6)->toDateString() . '&end_date=' . now()->subDays(3)->toDateString());

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    //TEST SEARCH POSTS BY TITLE
    public function test_search_posts_by_title()
    {
        Post::factory()->create(['title' => 'Laravel Tips']);
        Post::factory()->create(['title' => 'Healthy Living']);

        $response = $this->getJson('/api/posts?search=Laravel');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
}
