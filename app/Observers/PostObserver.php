<?php

namespace App\Observers;

use App\Jobs\NotifyUsers;
use App\Models\Post;
use App\Notifications\Posts;
use App\Services\Notifications;

class PostObserver
{
    /**
     * Handle the post "created" event.
     */
    public function created(Post $post): void
    {
        Notifications::notify(
            Notifications\Resources\Post::class,
            $post
        );
    }

    /**
     * Handle the post "updated" event.
     */
    public function updated(Post $post): void
    {
        //
    }

    /**
     * Handle the post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        //
    }

    /**
     * Handle the post "restored" event.
     */
    public function restored(Post $post): void
    {
        //
    }

    /**
     * Handle the post "force deleted" event.
     */
    public function forceDeleted(Post $post): void
    {
        //
    }
}
