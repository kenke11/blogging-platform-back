<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PostViewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function post_view()
    {
        Role::create(['name' => 'editor']);
        Role::create(['name' => 'user']);

        $editor = User::factory()->create();
        $user = User::factory()->create();

        $editor->assignRole('editor');
        $user->assignRole('user');

        $post = Post::factory()->create(['user_id' => $editor->id]);

        Sanctum::actingAs($user);

        $response = $this->postJson(route('posts.view', ['user' => $user->id, 'post' => $post->id]));

        $response->assertJson(['message' => 'user viewed this post', 'view' => true]);
    }

    /**
     * @test
     */
    public function post_already_viewed()
    {
        Role::create(['name' => 'editor']);
        Role::create(['name' => 'user']);

        $editor = User::factory()->create();
        $user = User::factory()->create();

        $editor->assignRole('editor');
        $user->assignRole('user');

        $post = Post::factory()->create(['user_id' => $editor->id]);

        Sanctum::actingAs($user);

        $response = $this->postJson(route('posts.view', ['user' => $user->id, 'post' => $post->id]));
        $response->assertJson(['message' => 'user viewed this post', 'view' => true]);

        $response = $this->postJson(route('posts.view', ['user' => $user->id, 'post' => $post->id]));
        $response->assertJson(['message' => 'user already viewed this post', 'view' => false]);
    }
}
