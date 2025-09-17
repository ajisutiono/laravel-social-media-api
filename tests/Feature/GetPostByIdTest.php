<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Database\Seeders\PassportSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetPostByIdTest extends TestCase
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
            'email' => $user->email,
            'password' => 'rahasia',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token']);

        return $response['access_token'];
    }

    #[Test]
    public function user_can_get_post_by_id()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia'),
        ]);

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $token = $this->loginAndGetToken($user);

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token,
        ])->getJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "data" => [
                    "id",
                    "user_id",
                    "title",
                    "content",
                    "created_at",
                    "updated_at",
                    "deleted_at",
                ],
            ]);
    }

    #[Test]
    public function guest_cannot_get_post_by_id_without_token()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia'),
        ]);

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $token = $this->loginAndGetToken($user);

        $response = $this->getJson("/api/posts/{$post->id}");

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    #[Test]
    public function user_cannot_get_wrong_id_post()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia'),
        ]);

        $token = $this->loginAndGetToken($user);

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token,
        ])->getJson("/api/posts/9999");

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
            ]);
    }
}
