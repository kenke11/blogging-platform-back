<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostDestroyRequest;
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function index(): JsonResponse
    {
        $posts = Post::with('user')
            ->withCount('views')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return response()->json(['posts' => $posts]);
    }

    public function store(PostStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Post::class);

        Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'user_id' => $request->user_id
        ]);

        return response()->json(['message' => 'Post created successfully']);
    }

    public function update(PostUpdateRequest $request, Post $post): JsonResponse
    {
        $this->authorize('update',  $post);

        $post->update([
            'title' => $request->title,
            'body' => $request->body,
            'published_date' => date($request->published_date)
        ]);

        return response()->json(['message' => 'Post edited successfully']);
    }

    public function destroy(PostDestroyRequest $request, Post $post): JsonResponse
    {
        Log::info($post);

        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
