<?php

namespace App\Events;

use App\Models\Post;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostViewEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private Post $post;
    private string $ip;
    private ?Authenticatable $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Post $post, string $ip, ?Authenticatable $user)
    {
        $this->post = $post;
        $this->ip = $ip;
        $this->user = $user;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getUser(): ?Authenticatable
    {
        return $this->user;
    }
}
