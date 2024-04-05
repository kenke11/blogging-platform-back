<?php

namespace Tests\Unit\Models;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PostTest extends TestCase
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

        $post = Post::factory()->create(['user_id' => $user->id]);

        $postUser = $post->user;

        $this->assertInstanceOf(User::class, $postUser);

        $this->assertEquals($user->id, $postUser->id);
    }

    /**
     * @test
     */
    public function test_views_relationship()
    {
        Role::create(['name' => 'editor']);
        $user = User::factory()->create();
        $user->assignRole('editor');

        $post = Post::factory()->create();

        $users = User::factory()->count(2)->create();

        $post->views()->attach($users);

        $postViews = $post->views;

        $this->assertInstanceOf(Collection::class, $postViews);

        $this->assertEquals(2, $postViews->count());
    }

    /**
     * @test
     */
    public function test_comments_relationship()
    {
        Role::create(['name' => 'editor']);
        $user = User::factory()->create();
        $user->assignRole('editor');

        $post = Post::factory()->create();

        Comment::factory()->count(3)->create(['user_id' => $user->id, 'post_id' => $post->id]);

        $postComments = $post->comments;

        $this->assertInstanceOf(Collection::class, $postComments);

        $this->assertEquals(3, $postComments->count());
    }
}
