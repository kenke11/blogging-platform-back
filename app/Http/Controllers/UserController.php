<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getAuthUser(): JsonResponse
    {
        $user = auth()->user();

        if ($user)
        {
            $userData = $user->toArray();
            $userData['role'] = $user->role();

            return response()->json(['user' => $userData]);
        }

        return response()->json(['message' => 'User unauthenticated'], 419);
    }
}
