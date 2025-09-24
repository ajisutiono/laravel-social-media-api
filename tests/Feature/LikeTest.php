<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\PassportSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LikeTest extends TestCase
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
    public function user_can_like_own_post()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia')
        ]);

        $token = $this->loginAndGetToken($user);

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token
        ])->postJson("/api/posts/{$post->id}/like");

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user_id',
                    'likeable_id',
                    'likeable_type',
                ],
            ]);
    }

    #[Test]
    public function user_can_like_own_comment()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia')
        ]);

        $token = $this->loginAndGetToken($user);

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token
        ])->postJson("/api/comments/{$comment->id}/like");

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user_id',
                    'likeable_id',
                    'likeable_type',
                ],
            ]);
    }


    #[Test]
    public function user_can_like_post_of_other_user()
    {
        $userA = User::factory()->create([
            'password' => bcrypt('rahasia')
        ]);

        $userB = User::factory()->create([
            'password' => bcrypt('rahasia')
        ]);

        $tokenUserB = $this->loginAndGetToken($userB);

        $postUserA = Post::factory()->create([
            'user_id' => $userA->id,
        ]);

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $tokenUserB
        ])->postJson("/api/posts/{$postUserA->id}/like");

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user_id',
                    'likeable_id',
                    'likeable_type',
                ],
            ]);
    }

    #[Test]
    public function user_can_like_comment_of_other_user()
    {
        $userA = User::factory()->create([
            'password' => bcrypt('rahasia')
        ]);

        $userB = User::factory()->create([
            'password' => bcrypt('rahasia')
        ]);

        $tokenUserB = $this->loginAndGetToken($userB);

        $postUserA = Post::factory()->create([
            'user_id' => $userA->id,
        ]);

        $commentUserA = Comment::factory()->create([
            'user_id' => $userA->id,
            'post_id' => $postUserA->id,
        ]);

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $tokenUserB
        ])->postJson("/api/comments/{$commentUserA->id}/like");

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user_id',
                    'likeable_id',
                    'likeable_type',
                ],
            ]);
    }

    #[Test]
    public function guest_cannot_like_post()
    {
        $post = Post::factory()->create();

        $response = $this->postJson("/api/posts/{$post->id}/like");

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    #[Test]
    public function guest_cannot_like_comment()
    {
        $comment = Comment::factory()->create();

        $response = $this->postJson("/api/comments/{$comment->id}/like");

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    #[Test]
    public function user_cannot_like_post_do_not_exist()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia')
        ]);

        $token = $this->loginAndGetToken($user);

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token
        ])->postJson("/api/posts/9999/like");

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
            ]);
    }


    #[Test]
    public function user_cannot_like_comment_do_not_exist()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia')
        ]);

        $token = $this->loginAndGetToken($user);

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token
        ])->postJson("/api/comments/9999/like");

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
            ]);
    }
}
