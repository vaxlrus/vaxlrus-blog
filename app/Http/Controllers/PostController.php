<?php

namespace App\Http\Controllers;

use App\Events\PostViewEvent;
use App\Models\Post;
use App\Services\PostViewsLogging\PostViewsLoggerService;
use Illuminate\Support\Facades\Request;

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

    public function show(Request $request, PostViewsLoggerService $service, Post $post)
    {
        PostViewEvent::dispatch($post, Request::ip(), auth()->user());

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
