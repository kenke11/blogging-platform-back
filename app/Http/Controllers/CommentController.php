<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentDestroyRequest;
use App\Http\Requests\CommentStoreRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function index(Post $post): JsonResponse
    {
        $comments = $post->comments()->with('user')->orderBy('created_at', 'desc')->get();

        return response()->json(['comments' => $comments]);
    }

    public function store(CommentStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Comment::class);

        $comment = Comment::create([
            'comment' => $request->comment,
            'user_id' => $request->user_id,
            'post_id' => $request->post_id
        ]);

        return response()->json(['message' => 'Comment created successfully', 'comment' => $comment]);
    }

    public function destroy(CommentDestroyRequest $request, Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
