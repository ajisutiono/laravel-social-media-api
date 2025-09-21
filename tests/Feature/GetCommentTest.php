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

class GetAllCommentsTest extends TestCase
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
    public function users_can_get_all_comments()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia')
        ]);

        $token = $this->loginAndGetToken($user);

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
        ]);

        $response =  $this->withHeaders([
            "Authorization" => "Bearer " . $token
        ])->getJson("/api/posts/{$post->id}/comments/{$comment->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data',
            ]);
    }

    #[Test]
    public function userB_can_view_all_comments_created_by_userA()
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
            'post_id' => $postUserA->id,
        ]);

        $response =  $this->withHeaders([
            "Authorization" => "Bearer " . $tokenUserB
        ])->getJson("/api/posts/{$postUserA->id}/comments/{$commentUserA->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data',
            ]);
    }

    #[Test]
    public function guest_cannot_get_all_comments()
    {
        $user = User::factory()->create();

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
        ]);

        $response =  $this->getJson("/api/posts/{$post->id}/comments/{$comment->id}");

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    #[Test]
    public function users_cannot_get_all_comments_not_exist()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia')
        ]);

        $token = $this->loginAndGetToken($user);

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $response =  $this->withHeaders([
            "Authorization" => "Bearer " . $token
        ])->getJson("/api/posts/{$post->id}/comments/9999");

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
            ]);
    }

    #[Test]
    public function users_cannot_get_comments_of_other_posts()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia')
        ]);

        $token = $this->loginAndGetToken($user);

        $postA = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $postB = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $commentA = Comment::factory()->create([
            'post_id' => $postA->id,
        ]);

        $response =  $this->withHeaders([
            "Authorization" => "Bearer " . $token
        ])->getJson("/api/posts/{$postB->id}/comments/{$commentA->id}");

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
            ]);
    }
}
