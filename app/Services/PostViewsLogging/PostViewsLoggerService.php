<?php

namespace App\Services\PostViewsLogging;

use App\Models\Post;
use App\Repositories\PostViewsDbRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;

class PostViewsLoggerService
{
    private PostViewsDbRepository $dbRepo;

    public function __construct(PostViewsDbRepository $dbRepo)
    {
        $this->dbRepo = $dbRepo;
    }

    /**
     * Залоггировать новый просмотр у поста
     *
     * @param Post $post
     * @param string $ip
     * @param Authenticatable|null $user
     * @return void
     */
    public function logNewView(Post $post, string $ip, ?Authenticatable $user): void
    {
        if ($this->doesClientSawPost($post, $ip, $user)) {
            return;
        }

        $this->markClientAsViewedPost($post, $ip, $user);
    }

    /**
     * Определить видел ли клиент пост раньше
     *
     * @param Post $post
     * @param string $ip
     * @param Authenticatable|null $user
     * @return bool
     */
    private function doesClientSawPost(Post $post, string $ip, ?Authenticatable $user): bool
    {
        if (isset($user)) {
            return (bool)$this->dbRepo->getPostViewDataByUser($user, $post);
        }

        return (bool)$this->dbRepo->getPostViewDataByIp($ip, $post);
    }

    /**
     * Отметить клиента как просмотревшего пост
     *
     * @param Post $post
     * @param string $ip
     * @param Authenticatable|null $user
     * @return void
     */
    private function markClientAsViewedPost(Post $post, string $ip, ?Authenticatable $user): void
    {
        if (isset($user)) {
            $this->dbRepo->setPostViewByUser($post, $user, $ip, Carbon::now());
            return;
        }

        $this->dbRepo->setPostViewByIp($post, $ip, Carbon::now());
    }

    /**
     * Получить количество просмотров на текущий день
     *
     * @param Post $post
     * @return int
     */
    public function getTodayViews(Post $post): int
    {
        return $this->dbRepo->getTodayViewsCount($post, Carbon::now());
    }

    /**
     * Получить количество просмотров за все время
     *
     * @param Post $post
     * @return int
     */
    public function getTotalViews(Post $post): int
    {
        return $this->dbRepo->getTotalViewsCount($post);
    }
}
