<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostViewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $posts = Post::all();

        $users->each(function ($user) use ($posts) {
            $user->posts()->attach(
                $posts->random(rand(1, 5))->pluck('id')->toArray()
            );
        });
    }
}
