<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PassportSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CreatePostTest extends TestCase
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
    public function user_can_create_post_successfully()
    {
        $user = User::factory()->create(['password' => bcrypt('rahasia')]);
        $token = $this->loginAndGetToken($user);

        $payload = [
            'title' => 'Title Testing',
            'content' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit.'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/posts', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Successfully added new post',
                'data' => [
                    'title' => $payload['title'],
                    'content' => $payload['content'],
                    'user_id' => $user->id,
                ]
            ]);

        $this->assertDatabaseHas('posts', $payload + ['user_id' => $user->id]);
    }

    #[Test]
    public function guest_cannot_create_post_without_token()
    {
        $payload = [
            'title' => 'No Token Post',
            'content' => 'Trying without token',
        ];

        $response = $this->postJson('/api/posts', $payload);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    #[Test]
    public function cannot_create_post_with_invalid_token()
    {
        $payload = [
            'title' => 'Invalid Token Post',
            'content' => 'This should fail',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer INVALID_TOKEN',
        ])->postJson('/api/posts', $payload);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    #[Test]
    public function cannot_create_post_without_title()
    {
        $user = User::factory()->create(['password' => bcrypt('rahasia')]);
        $token = $this->loginAndGetToken($user);

        $payload = ['content' => 'Only content, no title'];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/posts', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    #[Test]
    public function cannot_create_post_without_content()
    {
        $user = User::factory()->create(['password' => bcrypt('rahasia')]);
        $token = $this->loginAndGetToken($user);

        $payload = ['title' => 'Only title, no content'];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/posts', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content']);
    }

    #[Test]
    public function user_can_create_post_with_image()
    {
        Storage::fake('public');

        $user = User::factory()->create(['password' => bcrypt('rahasia')]);
        $token = $this->loginAndGetToken($user);

        $payload = [
            'title' => 'Post With Image',
            'content' => 'This post has an image.',
            'image' => UploadedFile::fake()->image('post.jpg')
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/posts', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => $payload['title']]);

        $imagePath = str_replace('/storage/', '', $response['data']['image']);
        $this->assertTrue(Storage::disk('public')->exists($imagePath));

        $this->assertDatabaseHas('posts', [
            'title'   => $payload['title'],
            'user_id' => $user->id,
        ]);
    }
}
