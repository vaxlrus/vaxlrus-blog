<?php

namespace App\Http\Controllers;

use App\Events\PostViewEvent;
use App\Models\Post;
use App\Models\PostViews;
use App\Services\PostViewsLoggerService;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function index()
    {
        return view('posts.index', [
            'posts' => Post::latest()->filter(
                        request(['search', 'category', 'author'])
                    )->paginate(18)->withQueryString()
        ]);
    }

    public function show(Post $post)
    {
        PostViewEvent::dispatch($post);

        return view('posts.show', [
            'post' => $post
        ]);
    }
}
