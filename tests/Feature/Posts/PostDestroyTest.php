<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PostDestroyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function post_deleted_successfully_by_owner()
    {
        Role::create(['name' => 'editor']);
        $editor = User::factory()->create();
        $editor->assignRole('editor');
        Sanctum::actingAs($editor);

        $post = Post::factory()->create(['user_id' => $editor->id]);

        $response = $this->postJson(route('posts.destroy', $post->id), [
            'user_id' => $editor->id
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Post deleted successfully']);
    }

    /**
     * @test
     */
    public function post_deleted_successfully_by_admin()
    {
        Role::create(['name' => 'editor']);
        Role::create(['name' => 'admin']);

        $editor = User::factory()->create();
        $admin = User::factory()->create();

        $editor->assignRole('editor');
        $admin->assignRole('admin');

        $post = Post::factory()->create(['user_id' => $editor->id]);

        Sanctum::actingAs($admin);

        $response = $this->postJson(route('posts.destroy', $post->id), [
            'user_id' => $admin->id
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Post deleted successfully']);
    }

    /**
     * @test
     */
    public function post_delete_by_user_and_get_permission_error()
    {
        Role::create(['name' => 'editor']);
        Role::create(['name' => 'user']);

        $editor = User::factory()->create();
        $user = User::factory()->create();

        $editor->assignRole('editor');
        $user->assignRole('user');

        $post = Post::factory()->create(['user_id' => $editor->id]);

        Sanctum::actingAs($user);

        $response = $this->postJson(route('posts.destroy', $post->id), [
            'user_id' => $user->id
        ]);

        $response->assertStatus(403)
            ->assertJson(['message' => 'This action is unauthorized.']);
    }

    /**
     * @test
     */
    public function post_delete_by_editor_on_other_post_and_get_permission_error()
    {
        Role::create(['name' => 'editor']);

        $editor = User::factory()->create();
        $user = User::factory()->create();

        $editor->assignRole('editor');
        $user->assignRole('editor');

        $post = Post::factory()->create(['user_id' => $editor->id]);

        Sanctum::actingAs($user);

        $response = $this->postJson(route('posts.destroy', $post->id), [
            'user_id' => $user->id
        ]);

        $response->assertStatus(403)
            ->assertJson(['message' => 'This action is unauthorized.']);
    }
}
