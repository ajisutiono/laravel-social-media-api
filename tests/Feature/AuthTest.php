<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PassportSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PassportSeeder::class);
    }

    #[Test]
    public function user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'fullname' => 'Testing',
            'email'    => 'testing@example.com',
            'password' => 'rahasia',
            'bio'      => 'Halo, I am user testing',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'testing@example.com']);
    }

    #[Test]
    public function user_can_login_and_get_token()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'rahasia',
        ]);


        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'access_token',
                'token_type',
                'expires_in',
                'user'
            ]);
    }

    #[Test]
    public function user_can_logout()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia'),
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'rahasia',
        ]);

        $token = $loginResponse['access_token'];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'status'  => 'success',
                'message' => 'Logged out successfully',
            ]);

        $this->assertDatabaseHas('oauth_access_tokens', [
            'user_id' => $user->id,
            'revoked' => true,
        ]);
    }
}
