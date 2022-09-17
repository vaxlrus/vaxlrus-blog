<?php

namespace App\Services\PostViewsLogging;

use App\Models\Post;
use App\Models\PostViews;
use App\Models\User;
use App\Repositories\PostViewsLogging\PostViewsDbRepo;
use App\Repositories\PostViewsLogging\PostViewsRedisRepo;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class PostViewsLoggerService
{
    private Post $post;
    private string $ip;
    private ?Authenticatable $user;
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
    public function handle(Post $post, string $ip, Authenticatable $user): void
    {
        $this->post = $post;
        $this->ip = $ip;
        $this->user = $user;

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
        if (isset($this->user)) {
            return;
        }

        // Если IP адрес просматривает пост не впервые
        if ($this->redisRepo->getLastViewedIp() === $this->ip) {
            return;
        }

        $this->redisRepo->setLastViewedIp($this->ip);
        $this->increasePostViewsCount();
    }

    /**
     * Обработка просмотра если это авторизованный пользователь
     *
     * @return void
     */
    private function authUsersHandler(): void
    {
        if (! isset($this->user)) {
            return;
        }

        // Если пользователь просматривает пост не впервые
        if ($this->redisRepo->isUserSawPost($this->user)) {
            return;
        }

        $this->redisRepo->markPostViewedByUser($this->user);
        $this->increasePostViewsCount();
    }

    /**
     * Увеличение количества просмотров у поста
     *
     * @return void
     */
    private function increasePostViewsCount(): void
    {
        // Если пост никто не просматривал, иницилизировать добавление данных
        if (! $this->dbRepo->isPostHaveViews($this->post)) {
            $this->dbRepo->handleViewsForNewPost($this->post);
        }

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
