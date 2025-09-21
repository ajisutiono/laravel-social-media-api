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

class UpdateCommentTest extends TestCase
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
    public function user_can_update_own_comment()
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
            'post_id' => $post->id
        ]);

        $payload = [
            'comment' => 'Update comment'
        ];

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token
        ])->patchJson("/api/posts/{$post->id}/comments/{$comment->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user_id',
                    'post_id',
                    'comment',
                ],
            ]);
    }

    #[Test]
    public function user_cannot_update_comments_of_other_user()
    {
        $userA = User::factory()->create([
            'password' => bcrypt('rahasia')
        ]);

        $userB = User::factory()->create([
            'password' => bcrypt('rahasia')
        ]);

        $tokenUserA = $this->loginAndGetToken($userA);

        $postUserB = Post::factory()->create([
            'user_id' => $userB->id,
        ]);

        $commentUserB = Comment::factory()->create([
            'user_id' => $userB->id,
            'post_id' => $postUserB->id
        ]);

        $payload = [
            'comment' => 'Update comment'
        ];

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $tokenUserA
        ])->patchJson("/api/posts/{$postUserB->id}/comments/{$commentUserB->id}", $payload);

        $response->assertStatus(403)
            ->assertJsonStructure([
                'message'
            ]);
    }

    #[Test]
    public function user_cannot_update_comment_without_value_comment()
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
            'post_id' => $post->id
        ]);

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token
        ])->patchJson("/api/posts/{$post->id}/comments/{$comment->id}");

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors',
            ])
            ->assertJson([
                "message" => "The comment field is required.",
                "errors" => [
                    "comment" => [
                        "The comment field is required."
                    ],
                ],
            ]);
    }

    #[Test]
    public function user_cannot_update_comment_that_does_not_exist()
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
        ])->patchJson("/api/posts/{$post->id}/comments/9999");

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
            ]);
    }

    #[Test]
    public function user_cannot_update_comment_when_post_does_not_exist()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia')
        ]);

        $token = $this->loginAndGetToken($user);

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
        ]);

        $payload = [
            'comment' => 'Update comment'
        ];

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token
        ])->patchJson("/api/posts/9999/comments/{$comment->id}", $payload);

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
            ]);
    }
}
