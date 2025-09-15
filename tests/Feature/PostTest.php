<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PassportSeeder;
use Faker\Provider\Lorem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PassportSeeder::class);
    }

    public function testPostSuccessfully()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasia'),
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'rahasia',
        ]);

        $token = $loginResponse['access_token'];

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

        $this->assertDatabaseHas('posts', [
            'title'   => $payload['title'],
            'content' => $payload['content'],
            'user_id' => $user->id,
        ]);
    }
}
