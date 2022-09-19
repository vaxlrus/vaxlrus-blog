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

    public Post $post;
    public string $ip;
    public ?Authenticatable $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Post $post, string $ip, Authenticatable $user = null)
    {
        $this->post = $post;
        $this->ip = $ip;
        $this->user = $user;
    }
}
