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

class CreateCommentTest extends TestCase
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
    public function users_can_create_comment()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia'),

        ]);

        $token = $this->loginAndGetToken($user);

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $comment = [
            'comment' => fake()->sentence,
        ];

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token,
        ])->postJson("/api/posts/{$post->id}/comments", $comment);


        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Successfully added new comment',
                'data' => [
                    'comment' => $comment['comment'],
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                ],
            ]);

        $this->assertDatabaseHas('comments', [
            'comment' => $comment['comment'],
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    #[Test]
    public function guest_cannot_create_comment()
    {
        $user = User::factory()->create();

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $comment = [
            'comment' => fake()->sentence,
        ];

        $response = $this->postJson("/api/posts/{$post->id}/comments", $comment);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    #[Test]
    public function users_cannot_create_comment_with_invalid_token()
    {
        $user = User::factory()->create();

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $comment = [
            'comment' => fake()->sentence,
        ];

        $response = $this->withHeaders([
            "Authorization" => "Bearer INVALID_TOKEN",
        ])->postJson("/api/posts/{$post->id}/comments", $comment);


        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    #[Test]
    public function users_cannot_create_comment_without_value_comment()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia'),

        ]);

        $token = $this->loginAndGetToken($user);

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token,
        ])->postJson("/api/posts/{$post->id}/comments");


        $response->assertStatus(422)
            ->assertJson(['message' => 'The comment field is required.']);
    }

    #[Test]
    public function users_cannot_create_comment_with_post_not_exist()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia'),

        ]);

        $token = $this->loginAndGetToken($user);

        $comment = [
            'comment' => fake()->sentence,
        ];

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token,
        ])->postJson("/api/posts/9999/comments", $comment);


        $response->assertStatus(404)
            ->assertJsonStructure(['message']);
    }
}
