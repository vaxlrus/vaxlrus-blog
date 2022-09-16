<?php

namespace App\Repositories\PostViewsLogging;

use App\Models\Post;
use App\Models\PostViews;

class PostViewsDbRepo
{
    private function handleNewPostViews(Post $post): void
    {
        if (PostViews::where('post_id', $post->id)->exists()) {
            return;
        }

        $post->views()->insert([
            'post_id' => $post->id,
            'count' => 1,
            'today' => 1
        ]);

    }

    public function increaseTotalViewsCount(Post $post): void
    {
        $this->handleNewPostViews($post);

        // Увеличить общее количество просмотров
        PostViews::where('post_id', $post->id)->increment('count', 1);
    }

    public function increaseTodayViewsCount(Post $post): void
    {
        $this->handleNewPostViews($post);

        // Увеличить сегодняшнее количество просмотров
        PostViews::where('post_id', $post->id)->increment('today', 1);
    }

    public function getTotalViewsCount(Post $post): int
    {
        return PostViews::where('post_id', $post->id)->first()->count ?? 0;
    }

    public function getTodayViewsCount(Post $post): int
    {
        return PostViews::where('post_id', $post->id)->first()->today ?? 0;
    }
}
