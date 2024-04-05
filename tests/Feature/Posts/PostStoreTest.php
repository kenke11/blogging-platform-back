<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PostStoreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function post_store_successfully()
    {
        Role::create(['name' => 'editor']);
        $editor = User::factory()->create();
        $editor->assignRole('editor');
        Sanctum::actingAs($editor);

        $postData = [
            'title' => 'New Post Title',
            'body' => 'New Post Body',
            'user_id' => $editor->id,
        ];

        $response = $this->postJson(route('posts.store'), $postData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Post created successfully']);

        $this->assertDatabaseHas('posts', $postData);
    }

    /**
     * @test
     */
    public function post_store_with_user()
    {
        Role::create(['name' => 'user']);
        $editor = User::factory()->create();
        $editor->assignRole('user');
        Sanctum::actingAs($editor);

        $postData = [
            'title' => 'New Post Title',
            'body' => 'New Post Body',
            'user_id' => $editor->id,
        ];

        $response = $this->postJson(route('posts.store'), $postData);

        $response->assertStatus(403)
            ->assertJson(['message' => 'This action is unauthorized.']);
    }

    /**
     * @test
     */
    public function post_store_with_admin()
    {
        Role::create(['name' => 'admin']);
        $editor = User::factory()->create();
        $editor->assignRole('admin');
        Sanctum::actingAs($editor);

        $postData = [
            'title' => 'New Post Title',
            'body' => 'New Post Body',
            'user_id' => $editor->id,
        ];

        $response = $this->postJson(route('posts.store'), $postData);

        $response->assertStatus(403)
            ->assertJson(['message' => 'This action is unauthorized.']);
    }

    /**
     * @test
     */
    public function post_store_validations()
    {
        Role::create(['name' => 'editor']);
        $editor = User::factory()->create();
        $editor->assignRole('editor');
        Sanctum::actingAs($editor);

        $postData = [
            'title' => '',
            'body' => '',
            'user_id' => '',
        ];

        $response = $this->postJson(route('posts.store'), $postData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title' => 'The title field is required.',
                'body' => 'The body field is required.',
                'user_id' => 'The user id field is required.'
            ]);

    }
}
