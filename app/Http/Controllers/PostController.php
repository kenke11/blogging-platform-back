<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostStoreRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    public function index(): JsonResponse
    {
        $posts = Post::with('user')
            ->withCount('views')
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
}
