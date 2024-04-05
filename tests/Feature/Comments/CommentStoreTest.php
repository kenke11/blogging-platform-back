<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CommentStoreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function comment_store_successfully()
    {
        Role::create(['name' => 'editor']);
        $editor = User::factory()->create();
        $editor->assignRole('editor');
        Sanctum::actingAs($editor);

        $post = Post::factory()->create(['user_id' => $editor->id]);

        $commentData = [
            'comment' => 'New Post Body',
            'user_id' => $editor->id,
            'post_id' => $post->id,
        ];

        $response = $this->postJson(route('comments.store'), $commentData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Comment created successfully']);

        $this->assertDatabaseHas('comments', $commentData);
    }

    /**
     * @test
     */
    public function comment_store_successfully_by_user()
    {
        Role::create(['name' => 'editor']);
        $editor = User::factory()->create();
        $editor->assignRole('editor');

        Role::create(['name' => 'user']);
        $user = User::factory()->create();
        $user->assignRole('editor');
        Sanctum::actingAs($user);

        $post = Post::factory()->create(['user_id' => $editor->id]);

        $commentData = [
            'comment' => 'New Post Body',
            'user_id' => $user->id,
            'post_id' => $post->id,
        ];

        $response = $this->postJson(route('comments.store'), $commentData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Comment created successfully']);

        $this->assertDatabaseHas('comments', $commentData);
    }

    /**
     * @test
     */
    public function comment_store_successfully_by_admin()
    {
        Role::create(['name' => 'editor']);
        $editor = User::factory()->create();
        $editor->assignRole('editor');

        Role::create(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin);

        $post = Post::factory()->create(['user_id' => $editor->id]);

        $commentData = [
            'comment' => 'New Post Body',
            'user_id' => $admin->id,
            'post_id' => $post->id,
        ];

        $response = $this->postJson(route('comments.store'), $commentData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Comment created successfully']);

        $this->assertDatabaseHas('comments', $commentData);
    }

    /**
     * @test
     */
    public function comment_store_validations()
    {
        Role::create(['name' => 'editor']);
        $editor = User::factory()->create();
        $editor->assignRole('editor');
        Sanctum::actingAs($editor);

        $post = Post::factory()->create(['user_id' => $editor->id]);

        $commentData = [
            'comment' => '',
            'user_id' => null,
            'post_id' => null,
        ];

        $response = $this->postJson(route('comments.store'), $commentData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'comment' => 'The comment field is required.',
                'user_id' => 'The user id field is required.',
                'post_id' => 'The post id field is required.'
            ]);
    }

    /**
     * @test
     */
    public function comment_store_by_unauthorized()
    {
        Role::create(['name' => 'editor']);
        $editor = User::factory()->create();
        $editor->assignRole('editor');

        $post = Post::factory()->create(['user_id' => $editor->id]);

        $commentData = [
            'comment' => 'New Post Body',
            'user_id' => $editor->id,
            'post_id' => $post->id,
        ];

        $response = $this->postJson(route('comments.store'), $commentData);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }
}
