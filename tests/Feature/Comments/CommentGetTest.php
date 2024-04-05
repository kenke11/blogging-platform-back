<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CommentGetTest extends TestCase
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

        $post = Post::factory()->create(['user_id' => $editor->id]);
        $comments = Comment::factory(10)->create(['user_id' => $editor->id, 'post_id' => $post->id]);

        $response = $this->getJson(route('comments.index', $post->id));

        $response->assertStatus(200);

        $responseComments = $response->json('comments');

        $this->assertCount(10, $comments);

        $response->assertJsonStructure([
            'comments' => [
                '*' => [
                    'id',
                    'comment',
                    'user_id',
                    'post_id',
                    'created_at',
                    'updated_at',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]
        ]);
    }
}
