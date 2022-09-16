<?php

namespace App\Http\Controllers;

use App\Events\PostViewEvent;
use App\Models\Post;
use App\Services\PostViewsLogging\PostViewsLoggerService;

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

    public function show(PostViewsLoggerService $service, Post $post)
    {
        PostViewEvent::dispatch($post);

        return view('posts.show', [
            'post' => $post,
            'post_data' => [
                'views' => [
                    'today' => $service->getTodayViews($post),
                    'total' => $service->getTotalViews($post)
                ]
            ]
        ]);
    }
}
