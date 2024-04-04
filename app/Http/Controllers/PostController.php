<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')
            ->withCount('views')
            ->paginate(5);

        return response()->json(['posts' => $posts]);
    }
}
