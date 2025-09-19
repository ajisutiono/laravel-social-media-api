<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Database\Seeders\PassportSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\Console\RetryCommand;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UpdatePostTest extends TestCase
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
    public function users_can_update_posts_their_own()
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
        ])->patchJson("/api/posts/{$post->id}", [
            "title" => "Edit Title",
            "content" => "Edit content",
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data',
            ]);
    }

    #[Test]
    public function users_cannot_update_posts_that_are_not_their_own()
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
            "Authorization" => "Bearer " . $tokenB,
        ])->patchJson("/api/posts/{$postA->id}", [
            "title" => "Edit Title post user A from user B",
            "content" => "Edit content post user A from user B",
        ]);

        $response->assertStatus(403)
            ->assertJsonStructure([
                'message'
            ]);
    }

    #[Test]
    public function users_cannot_update_posts_their_own_without_title()
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
        ])->patchJson("/api/posts/{$post->id}", [
            "content" => "Edit content",
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors',
            ]);
    }

    #[Test]
    public function users_cannot_update_posts_their_own_without_content()
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
        ])->patchJson("/api/posts/{$post->id}", [
            "title" => "Edit Title",
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors',
            ]);
    }

    #[Test]
    public function users_cannot_update_posts_that_do_not_exist()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia'),
        ]);

        $token = $this->loginAndGetToken($user);

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token,
        ])->patchJson("/api/posts/9999", [
            'title' => 'Some title',
            'content' => 'Some content',
        ]);

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
            ]);
    }

    #[Test]
    public function user_can_update_post_with_new_image()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'password' => bcrypt('rahasia'),
        ]);

        $token = $this->loginAndGetToken($user);

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $payload = [
            "title"   => "Edit Title",
            "content" => "Edit content",
            "image"   => UploadedFile::fake()->image('edit-image.jpg')
        ];

        $response = $this->withHeaders([
            "Authorization" => "Bearer " . $token,
        ])->patch("/api/posts/{$post->id}", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully updated post',
                'data' => [
                    'id'      => $post->id,
                    'title'   => $payload['title'],
                    'content' => $payload['content'],
                    'user_id' => $user->id,
                ],
            ]);

        $imagePath = str_replace('/storage/', '', $response->json('data.image'));

        $this->assertFileExists(Storage::disk('public')->path($imagePath));

        $this->assertDatabaseHas('posts', [
            'id'      => $post->id,
            'title'   => $payload['title'],
            'content' => $payload['content'],
            'image'   => $imagePath,
            'user_id' => $user->id,
        ]);
    }
}
