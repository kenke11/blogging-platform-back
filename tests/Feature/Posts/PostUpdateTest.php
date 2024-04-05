<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PostUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function post_update_successfully()
    {
        Role::create(['name' => 'editor']);
        $editor = User::factory()->create();
        $editor->assignRole('editor');
        Sanctum::actingAs($editor);

        $post = Post::factory()->create(['user_id' => $editor->id]);

        $updatedData = [
            'title' => 'Updated Post Title',
            'body' => 'Updated Post Body',
            'published_date' => now()->format('Y-m-d'),
        ];

        $response = $this->putJson(route('posts.update', $post->id), $updatedData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Post edited successfully']);

        $this->assertDatabaseHas('posts', array_merge(['id' => $post->id], $updatedData));
    }

    /**
     * @test
     */
    public function post_update_with_out_permission()
    {
        Role::create(['name' => 'editor']);
        Role::create(['name' => 'user']);
        $editor = User::factory()->create();
        $editor->assignRole('user');
        Sanctum::actingAs($editor);

        $post = Post::factory()->create(['user_id' => $editor->id]);

        $updatedData = [
            'title' => 'Updated Post Title',
            'body' => 'Updated Post Body',
            'published_date' => now()->format('Y-m-d'),
        ];

        $response = $this->putJson(route('posts.update', $post->id), $updatedData);

        $response->assertStatus(403)
            ->assertJson(['message' => 'This action is unauthorized.']);
    }

    /**
     * @test
     */
    public function post_update_validations()
    {
        Role::create(['name' => 'editor']);
        $editor = User::factory()->create();
        $editor->assignRole('editor');
        Sanctum::actingAs($editor);

        $post = Post::factory()->create(['user_id' => $editor->id]);

        $updatedData = [
            'title' => '',
            'body' => '',
            'published_date' => '',
        ];

        $response = $this->putJson(route('posts.update', $post->id), $updatedData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title' => 'The title field is required.',
                'body' => 'The body field is required.',
                'published_date' => 'The published date field is required.'
            ]);
    }

    /**
     * @test
     */
    public function post_update_unauthorized()
    {
        Role::create(['name' => 'editor']);
        $editor = User::factory()->create();
        $editor->assignRole('editor');

        $post = Post::factory()->create(['user_id' => $editor->id]);

        $updatedData = [
            'title' => 'Updated Post Title',
            'body' => 'Updated Post Body',
            'published_date' => now()->format('Y-m-d'),
        ];

        $response = $this->putJson(route('posts.update', $post->id), $updatedData);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }
}
