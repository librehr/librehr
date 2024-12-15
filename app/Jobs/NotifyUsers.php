<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\Posts;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyUsers implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
        User::query()
            ->where('active', true)
            ->chunkById(500, function ($users) {
                foreach ($users as $user) {
                    $user->notify(
                        Posts::make()
                            ->title('heee')
                            ->setData(data_get($user, 'email'), $this->data)
                            ->toDatabase()
                    );
                }
            });
    }
}
