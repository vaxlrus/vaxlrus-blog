<?php

namespace App\Services\PostViewsLogging;

use App\Models\Post;
use App\Repositories\PostViewsDbRepository;
use Illuminate\Contracts\Auth\Authenticatable;

class PostViewsLoggerService
{
    private Post $post;
    private string $ip;
    private ?Authenticatable $user;
    private PostViewsDbRepository $dbRepo;

    public function __construct(PostViewsDbRepository $dbRepo)
    {
        $this->dbRepo = $dbRepo;
    }

    /**
     * Залоггировать новый просмотр у поста
     *
     * @param Post $post
     * @return void
     */
    public function handle(Post $post, string $ip, Authenticatable $user = null): void
    {
        $this->post = $post;
        $this->ip = $ip;
        $this->user = $user;

        if ($this->doesClientSawPost()) {
            return;
        }

        $this->markClientAsViewedPost();
    }

    /**
     * Определить видел ли клиент пост раньше
     *
     * @return bool
     */
    private function doesClientSawPost(): bool
    {
        if (isset($this->user)) {
            return (bool)$this->dbRepo->getPostViewDataByUser($this->user, $this->post);
        }

        return (bool)$this->dbRepo->getPostViewDataByIp($this->ip, $this->post);
    }

    /**
     * Отметить клиента как просмотревшего пост
     *
     * @return void
     */
    private function markClientAsViewedPost(): void
    {
        if (isset($this->user)) {
            $this->dbRepo->setPostViewByUser($this->post, $this->user, $this->ip);
            return;
        }

        $this->dbRepo->setPostViewByIp($this->post, $this->ip);
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
