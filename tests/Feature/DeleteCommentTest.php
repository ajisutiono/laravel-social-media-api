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

class DeleteCommentTest extends TestCase
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
    public function user_can_delete_own_comment()
    {
        $user = User::factory()->create([
            'password' => 'rahasia'
        ]);

        $token = $this->loginAndGetToken($user);

        $post = Post::factory()->create([
            'user_id' => $user->id
        ]);

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/posts/{$post->id}/comments/{$comment->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully deleted comment',
                'data' => [
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                    'comment' => $comment->comment,
                ]
            ]);
    }

    #[Test]
    public function post_owner_can_delete_comments_from_other_users()
    {
        $userA = User::factory()->create([
            'password' => 'rahasia'
        ]);

        $tokenUserA = $this->loginAndGetToken($userA);

        $userB = User::factory()->create([
            'password' => 'rahasia'
        ]);

        $postUserA = Post::factory()->create([
            'user_id' => $userA->id
        ]);

        $commentUserBToPostUserA = Comment::factory()->create([
            'user_id' => $userB->id,
            'post_id' => $postUserA->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $tokenUserA
        ])->deleteJson("/api/posts/{$postUserA->id}/comments/{$commentUserBToPostUserA->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully deleted comment',
                'data' => [
                    'user_id' => $userB->id,
                    'post_id' => $postUserA->id,
                    'comment' => $commentUserBToPostUserA->comment,
                ]
            ]);
    }

    #[Test]
    public function user_cannot_delete_comment_owned_by_another_user()
    {
        $userA = User::factory()->create([
            'password' => 'rahasia'
        ]);

        $userB = User::factory()->create([
            'password' => 'rahasia'
        ]);

        $tokenUserB = $this->loginAndGetToken($userB);

        $postUserA = Post::factory()->create([
            'user_id' => $userA->id
        ]);

        $commentUserA = Comment::factory()->create([
            'user_id' => $userA->id,
            'post_id' => $postUserA->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $tokenUserB
        ])->deleteJson("/api/posts/{$postUserA->id}/comments/{$commentUserA->id}");

        $response->assertStatus(403)
            ->assertJsonStructure([
                'message'
            ]);
    }

    #[Test]
    public function user_cannot_delete_comment_does_not_exist()
    {
        $user = User::factory()->create([
            'password' => 'rahasia'
        ]);

        $token = $this->loginAndGetToken($user);

        $post = Post::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/posts/{$post->id}/comments/9999");

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
            ]);
    }

    #[Test]
    public function user_cannot_delete_comment_when_post_does_not_exist()
    {
        $user = User::factory()->create([
            'password' => 'rahasia'
        ]);

        $token = $this->loginAndGetToken($user);

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
        ]);


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/posts/9999/comments/{$comment->id}");

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
            ]);
    }

    #[Test]
    public function guest_cannot_delete_comment()
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->create();

        $response = $this->deleteJson("/api/posts/{$post->id}/comments/{$comment->id}");

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }
}
