<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PostGetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function post_get_return_all_posts()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'editor']);
        $user->assignRole('editor');

        Post::factory(5)->create(['user_id' => $user->id]);

        $response = $this->getJson(route('posts.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'posts' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'body',
                            'user',
                            'views_count',
                            'published_date',
                            'created_at',
                            'updated_at',
                        ]
                    ],
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total',
                ]
            ]);
    }

    /**
     * @test
     */
    public function post_get_return_all_posts_for_editor()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'editor']);
        $user->assignRole('editor');
        Sanctum::actingAs($user);

        Post::factory(5)->create(['user_id' => $user->id]);

        $response = $this->getJson(route('posts.get_editor_posts', $user->id));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'posts' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'body',
                            'user',
                            'views_count',
                            'published_date',
                            'created_at',
                            'updated_at',
                        ]
                    ],
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total',
                ]
            ]);
    }

    /**
     * @test
     */
    public function post_get_return_all_posts_for_user_with_out_permission()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'user']);
        Role::create(['name' => 'editor']);
        $user->assignRole('user');
        Sanctum::actingAs($user);

        Post::factory(5)->create(['user_id' => $user->id]);

        $response = $this->getJson(route('posts.get_editor_posts', $user->id));

        $response->assertStatus(403)
            ->assertJson(['message' => 'This action is unauthorized.']);
    }
}
