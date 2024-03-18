<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\Posts;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class TestJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $data)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger(now() . ' do nothing.');
    }
}
