<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Database\Seeders\PassportSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class GetAllPostsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PassportSeeder::class);
    }

    private function loginAndGetToken(User $user): string
    {
        $response = $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'rahasia',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token']);

        return $response['access_token'];
    }

    #[Test]
    public function user_can_get_all_posts()
    {
        $user = User::factory()->create([
            "password" => bcrypt("rahasia")
        ]);

        Post::factory()->count(3)->create(['user_id' => $user->id]);

        $token = $this->loginAndGetToken($user);

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token,
        ])->getJson("/api/posts");

        $response->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "data" => [
                    "*" => [
                        "id",
                        "user_id",
                        "title",
                        "content",
                        "created_at",
                        "updated_at",
                        "deleted_at",
                    ],
                ],
            ]);
    }

    #[Test]
    public function user_cannot_get_all_posts_invalid_token()
    {
        $response = $this->withHeaders([
             'Authorization' => 'Bearer INVALID_TOKEN',
        ])->getJson('/api/posts');

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

}
