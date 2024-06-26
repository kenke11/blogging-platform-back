<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    public function editorPosts(User $user): bool
    {
        return $user->hasRole('editor');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user): \Illuminate\Auth\Access\Response|bool
    {
        return $user->hasRole('editor');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Post $post): \Illuminate\Auth\Access\Response|bool
    {
        if ($user->hasRole('editor') && $user->id !== +$post->user_id){
            return false;
        }

        return $user->hasRole('editor');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Post $post): \Illuminate\Auth\Access\Response|bool
    {
        if ($user->hasRole('editor') && $user->id !== +$post->user_id){
            return false;
        }

        return $user->hasRole(['admin', 'editor']);
    }
}
