<?php

namespace App\Repositories;

use App\Models\Post;
use App\Models\PostView;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;

class PostViewsDbRepository
{
    /**
     * Получить количество просмотров за все время
     *
     * @param Post $post
     * @return int
     */
    public function getTotalViewsCount(Post $post): int
    {
        return PostView::where('post_id', '=', $post->id)->count();
    }

    /**
     * Получить количество просмотров за сегодня
     *
     * @param Post $post
     * @param Carbon $date
     * @return int
     */
    public function getTodayViewsCount(Post $post, Carbon $date): int
    {
        return PostView::where('post_id', '=', $post->id)->where('view_date', '=', $date->toDateString())->count();
    }

    /**
     * Получить информацию о просмотре используя модель пользователя
     *
     * @param Authenticatable $user
     * @param Post $post
     * @return PostView|null
     */
    public function getPostViewDataByUser(Authenticatable $user, Post $post): ?PostView
    {
        return PostView::where('post_id', $post->id)
                        ->where('user_id', $user->id)
                        ->first();
    }

    /**
     * Получить информацию о просмотре поста по IP
     *
     * @param string $ip
     * @param Post $post
     * @return PostView|null
     */
    public function getPostViewDataByIp(string $ip, Post $post): ?PostView
    {
        return PostView::where('post_id', $post->id)
                        ->where('ip', $ip)
                        ->first();
    }

    /**
     * Отметить пост просмотренным пользователем
     *
     * @param Post $post
     * @param Authenticatable $user
     * @param string $ip
     * @param Carbon $date
     * @return void
     */
    public function setPostViewByUser(Post $post, Authenticatable $user, string $ip, Carbon $date): void
    {
        PostView::insert([
            'post_id' => $post->id,
            'user_id' => $user->getAuthIdentifier(),
            'ip' => $ip,
            'view_date' => $date->toDateString()
        ]);
    }

    /**
     * Отметить пост просмотренным незарегистрированным клиентом
     *
     * @param Post $post
     * @param string $ip
     * @param Carbon $date
     * @return void
     */
    public function setPostViewByIp(Post $post, string $ip, Carbon $date)
    {
        PostView::insert([
            'user_id' => null,
            'post_id' => $post->id,
            'ip' => $ip,
            'view_date' => $date->toDateString()
        ]);
    }
}
