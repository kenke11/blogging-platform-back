<?php

namespace Tests\Unit\Models;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_posts_relationship()
    {
        Role::create(['name' => 'editor']);
        $user = User::factory()->create();
        $user->assignRole('editor');

        Post::factory()->count(3)->create(['user_id' => $user->id]);

        $userPosts = $user->posts;

        $this->assertInstanceOf(Collection::class, $userPosts);

        $this->assertEquals(3, $userPosts->count());
    }

    /**
     * @test
     */
    public function test_views_relationship()
    {
        Role::create(['name' => 'editor']);
        $user = User::factory()->create();
        $user->assignRole('editor');

        $posts = Post::factory()->count(2)->create();

        $user->views()->attach($posts);

        $userViews = $user->views;

        $this->assertInstanceOf(Collection::class, $userViews);

        $this->assertEquals(2, $userViews->count());
    }

    /**
     * @test
     */
    public function test_comments_relationship()
    {
        Role::create(['name' => 'editor']);
        $user = User::factory()->create();
        $user->assignRole('editor');

        $post = Post::factory()->create(['user_id' => $user]);

        Comment::factory()->count(3)->create(['user_id' => $user->id, 'post_id' => $post->id]);

        $userComments = $user->comments;

        $this->assertInstanceOf(Collection::class, $userComments);

        $this->assertEquals(3, $userComments->count());
    }

    /**
     * @test
     */
    public function testRoleMethod()
    {
        $user = User::factory()->create();

        $role = Role::create(['name' => 'admin']);
        $user->assignRole($role);

        $userRole = $user->role();

        $this->assertEquals('admin', $userRole);
    }
}
