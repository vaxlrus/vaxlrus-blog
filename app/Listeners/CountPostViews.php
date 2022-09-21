<?php

namespace App\Listeners;

use App\Events\PostViewEvent;
use App\Services\PostViewsLogging\PostViewsLoggerService;
use Illuminate\Contracts\Queue\ShouldQueue;

class CountPostViews implements ShouldQueue
{
    private PostViewsLoggerService $service;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(PostViewsLoggerService $postViewsLoggerService)
    {
        $this->service = $postViewsLoggerService;
    }

    /**
     * Handle the event.
     *
     * @param  PostViewEvent  $event
     * @return void
     */
    public function handle(PostViewEvent $event)
    {
        $this->service->logNewView($event->getPost(), $event->getIp(), $event->getUser());
    }
}
