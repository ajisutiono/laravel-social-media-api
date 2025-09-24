<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\PassportSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class GetAllLikesTest extends TestCase
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
    public function user_can_view_all_likes_post()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia')
        ]);

        $token = $this->loginAndGetToken($user);

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $likes = Like::factory()
            ->count(5)
            ->for($post, 'likeable')
            ->sequence(fn() => ['user_id' => User::factory()])
            ->create();

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token
        ])->getJson("/api/posts/{$post->id}/likes");

        $response->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "data",
            ]);

        $this->assertCount(5, $response["data"]);
    }


    #[Test]
    public function guest_cannot_view_likes_post()
    {
        $post = Post::factory()->create();

        $likes = Like::factory()
            ->count(5)
            ->for($post, 'likeable')
            ->sequence(fn() => ['user_id' => User::factory()])
            ->create();

        $response = $this->getJson("/api/posts/{$post->id}/likes");


        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    #[Test]
    public function user_can_view_all_likes_comment()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia')
        ]);

        $token = $this->loginAndGetToken($user);

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
        ]);

        $likes = Like::factory()
            ->count(5)
            ->for($comment, 'likeable')
            ->sequence(fn() => ['user_id' => User::factory()])
            ->create();

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token
        ])->getJson("/api/comments/{$comment->id}/likes");

        $response->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "data",
            ]);

        $this->assertCount(5, $response["data"]);
    }


    #[Test]
    public function guest_cannot_view_likes_comment()
    {
        $comment = Comment::factory()->create();

        $likes = Like::factory()
            ->count(5)
            ->for($comment, 'likeable')
            ->sequence(fn() => ['user_id' => User::factory()])
            ->create();

        $response = $this->getJson("/api/comments/{$comment->id}/likes");


        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }
}
