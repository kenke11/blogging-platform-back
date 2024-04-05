<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CommentDestroyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function comment_destroy_successfully_by_owner()
    {
        Role::create(['name' => 'editor']);
        $editor = User::factory()->create();
        $editor->assignRole('editor');
        Sanctum::actingAs($editor);

        $post = Post::factory()->create(['user_id' => $editor->id]);
        $comment = Comment::factory()->create(['user_id' => $editor->id, 'post_id' => $post->id]);

        $response = $this->postJson(route('comments.destroy', $comment->id), [
            'user_id' => $editor->id,
            'post_id' => $post->id
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Comment deleted successfully']);
    }

    /**
     * @test
     */
    public function comment_deleted_successfully_by_admin()
    {
        Role::create(['name' => 'editor']);
        Role::create(['name' => 'admin']);

        $editor = User::factory()->create();
        $admin = User::factory()->create();

        $editor->assignRole('editor');
        $admin->assignRole('admin');

        $post = Post::factory()->create(['user_id' => $editor->id]);
        $comment = Comment::factory()->create(['user_id' => $editor->id, 'post_id' => $post->id]);

        Sanctum::actingAs($admin);

        $response = $this->postJson(route('comments.destroy', $comment->id), [
            'user_id' => $admin->id,
            'post_id' => $post->id
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Comment deleted successfully']);
    }

    /**
     * @test
     */
    public function comment_deleting_by_user_on_other_comment()
    {
        Role::create(['name' => 'editor']);
        Role::create(['name' => 'user']);

        $editor = User::factory()->create();
        $user = User::factory()->create();

        $editor->assignRole('editor');
        $user->assignRole('user');

        $post = Post::factory()->create(['user_id' => $editor->id]);
        $comment = Comment::factory()->create(['user_id' => $editor->id, 'post_id' => $post->id]);

        Sanctum::actingAs($user);

        $response = $this->postJson(route('comments.destroy', $comment->id), [
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);

        $response->assertStatus(403)
            ->assertJson(['message' => 'This action is unauthorized.']);
    }
}
