<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class AuthTest extends TestCase
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

    // TEST USER REGISTRATION
    public function test_user_can_register_successfully()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(201)
            ->assertJson(['message' => 'User registered successfully']);

        $this->assertDatabaseHas('users', ['email' => 'johndoe@example.com']);
    }

    // TEST REGISTRATION FAILS WITH MISSING FIELDS
    public function test_registration_fails_with_missing_fields()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    // TEST REGISTRATION FAILS WITH INVALID EMAIL
    public function test_registration_fails_with_invalid_email()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    // TEST USER LOGIN SUCCESS
    public function test_user_can_login_successfully()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    // TEST LOGIN FAILS WITH WRONG PASSWORD
    public function test_login_fails_with_wrong_password()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Invalid credentials']);
    }

    // TEST LOGIN FAILS WITH MISSING FIELDS
    public function test_login_fails_with_missing_fields()
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }
}
