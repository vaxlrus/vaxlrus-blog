<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostViews;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Request;

class PostViewsLoggerService
{
    private Post $post;

    public function handle(Post $post): void
    {
        $this->post = $post;

        $this->guestsHandler();
        $this->authUsersHandler();
    }

    private function guestsHandler(): void
    {
        if (! auth()->guest()) {
            return;
        }

        $key = "post:{$this->post->id}:views.last_viewed_ip";
        $lastPostViewedIp = Redis::get($key);

        // Если IP адрес просматривает пост не впервые
        if ($lastPostViewedIp === Request::ip()) {
            return;
        }

        Redis::set($key, Request::ip());
        $this->increasePostViewsCount();
    }

    private function authUsersHandler(): void
    {
        if (! $user = auth()->user()) {
            return;
        }

        $key = "post:{$this->post->id}:views:users:{$user->id}";

        // Если пользователь просматривает пост не впервые
        if (Redis::get($key)) {
            return;
        }

        Redis::set($key, true);
        $this->increasePostViewsCount();
    }

    private function increasePostViewsCount(): void
    {
        // Если у поста не существует просмотров
        if (!PostViews::where('post_id', $this->post->id)->exists()) {
            $this->post->views()->insert([
                'post_id' => $this->post->id,
                'count' => 1
            ]);
        }

        PostViews::where('post_id', $this->post->id)->increment('count', 1);
    }
}
