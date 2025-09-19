<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Database\Seeders\PassportSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DeletePostTest extends TestCase
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
            'password' => 'rahasia'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token']);

        return $response['access_token'];
    }

    #[Test]
    public function users_can_delete_their_own_posts()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia')
        ]);

        $token = $this->loginAndGetToken($user);

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data',
            ]);
    }

    #[Test]
    public function users_cannot_delete_posts_that_are_not_their_own()
    {
        $userA = User::factory()->create([
            'password' => bcrypt('rahasia'),
        ]);

        $postA = Post::factory()->create([
            'user_id' => $userA->id
        ]);

        $userB = User::factory()->create([
            'password' => bcrypt('rahasia'),
        ]);

        $tokenB = $this->loginAndGetToken($userB);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $tokenB
        ])->deleteJson("/api/posts/{$postA->id}");

        $response->assertStatus(403)
            ->assertJsonStructure([
                'message'
            ]);
    }

    #[Test]
    public function users_cannot_delete_posts_that_do_not_exist()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia'),
        ]);

        $token = $this->loginAndGetToken($user);

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token,
        ])->deleteJson("/api/posts/9999");

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
            ]);
    }

    #[Test]
    public function guest_cannot_delete_posts()
    {
       $post = Post::factory()->create();

        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }
}
