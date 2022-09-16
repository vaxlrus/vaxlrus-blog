<?php

namespace App\Repositories\PostViewsLogging;

use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

class PostViewsRedisRepo
{
    private Post $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function getLastViewedIp(): ?string
    {
        $key = "post:{$this->post->id}:views:last_viewed_ip";

        return Redis::get($key) ?? null;
    }

    public function setLastViewedIp(string $ip): void
    {
        $key = "post:{$this->post->id}:views:last_viewed_ip";

        Redis::set($key, $ip);
    }

    public function isUserSawPost(User|Authenticatable $user): bool
    {
        $key = "post:{$this->post->id}:views:users:{$user->id}";

        return (bool)Redis::get($key);
    }

    public function markPostViewedByUser(User|Authenticatable $user): void
    {
        $key = "post:{$this->post->id}:views:users:{$user->id}";

        Redis::set($key, true);
    }

    public function setCurrentDate(Carbon $today): void
    {
        $key = "post:{$this->post->id}:views:current_date";

        Redis::set($key, $today->toDayDateTimeString());
    }

    public function getCurrentDate(): ?Carbon
    {
        $key = "post:{$this->post->id}:views:current_date";

        if (! Redis::get($key)) {
            return null;

        }

        return Carbon::createFromTimeString(Redis::get($key));
    }
}
