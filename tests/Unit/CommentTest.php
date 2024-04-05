<?php

namespace Tests\Unit\Models;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_user_relationship()
    {
        Role::create(['name' => 'editor']);
        $user = User::factory()->create();
        $user->assignRole('editor');

        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id, 'post_id' => $post->id]);

        $commentUser = $comment->user;

        $this->assertInstanceOf(User::class, $commentUser);

        $this->assertEquals($user->id, $commentUser->id);
    }

    /**
     * @test
     */
    public function test_post_relationship()
    {
        Role::create(['name' => 'editor']);
        $user = User::factory()->create();
        $user->assignRole('editor');

        $post = Post::factory()->create();

        $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $user->id]);

        $commentPost = $comment->post;

        $this->assertInstanceOf(Post::class, $commentPost);

        $this->assertEquals($post->id, $commentPost->id);
    }
}
