<?php

namespace App\Services\PostViewsLogging;

use App\Models\Post;
use App\Models\PostViews;
use App\Models\User;
use App\Repositories\PostViewsLogging\PostViewsDbRepo;
use App\Repositories\PostViewsLogging\PostViewsRedisRepo;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;

class PostViewsLoggerService
{
    private Post $post;
    private PostViewsRedisRepo $redisRepo;
    private PostViewsDbRepo $dbRepo;

    public function __construct(PostViewsRedisRepo $redisRepo, PostViewsDbRepo $dbRepo)
    {
        $this->redisRepo = $redisRepo;
        $this->dbRepo = $dbRepo;
    }

    /**
     * Залоггировать новый просмотр у поста
     *
     * @param Post $post
     * @return void
     */
    public function handle(Post $post): void
    {
        $this->post = $post;

        $this->guestsHandler();
        $this->authUsersHandler();
    }

    /**
     * Обработка прссмотра если это обычный неавторизованный посетитель
     *
     * @return void
     */
    private function guestsHandler(): void
    {
        if (!auth()->guest()) {
            return;
        }

        // Если IP адрес просматривает пост не впервые
        if ($this->redisRepo->getLastViewedIp() === Request::ip()) {
            return;
        }

        $this->redisRepo->setLastViewedIp(Request::ip());
        $this->increasePostViewsCount();
    }

    /**
     * Обработка просмотра если это авторизованный пользователь
     *
     * @return void
     */
    private function authUsersHandler(): void
    {
        if (!$user = auth()->user()) {
            return;
        }

        // Если пользователь просматривает пост не впервые
        if ($this->redisRepo->isUserSawPost($user)) {
            return;
        }

        $this->redisRepo->markPostViewedByUser($user);
        $this->increasePostViewsCount();
    }

    /**
     * Увеличение количества просмотров у поста
     *
     * @return void
     */
    private function increasePostViewsCount(): void
    {
        // Увеличить общее число просмотров
        $this->dbRepo->increaseTotalViewsCount($this->post);

        // Если уже наступил новый день
        if (Carbon::now() !== $this->redisRepo->getCurrentDate()) {
            $this->redisRepo->setCurrentDate(Carbon::now());
            $this->dbRepo->increaseTodayViewsCount($this->post);
        }
    }

    /**
     * Получить количество просмотров на текущий день
     *
     * @return int
     */
    public function getTodayViews(Post $post): int
    {
        return $this->dbRepo->getTodayViewsCount($post);
    }

    /**
     * Получить количество просмотров за все время
     *
     * @return int
     */
    public function getTotalViews(Post $post): int
    {
        return $this->dbRepo->getTotalViewsCount($post);
    }
}
