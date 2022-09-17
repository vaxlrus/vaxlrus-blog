<?php

namespace App\Repositories\PostViewsLogging;

use App\Models\Post;
use App\Models\PostViews;

class PostViewsDbRepo
{
    public function isPostHaveViews(Post $post): bool
    {
        return PostViews::where('post_id', $post->id)->exists();
    }

    public function handleViewsForNewPost(Post $post): bool
    {
        return $post->views()->insert([
            'post_id' => $post->id,
            'count' => 0,
            'today' => 0
        ]);
    }

    public function increaseTotalViewsCount(Post $post): void
    {
        // Увеличить общее количество просмотров
        PostViews::where('post_id', $post->id)->increment('count', 1);
    }

    public function increaseTodayViewsCount(Post $post): void
    {
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
