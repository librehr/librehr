<?php

namespace App\Console\Commands;

use App\Jobs\TestJob;
use App\Models\Document;
use App\Models\User;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Redis;

class testing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //$e = \Cache::store('redis')->get('librehr_database_batch:9b908b75-eddb-4bc7-9333-38fe674e9702');

        Bus::batch(
            Collection::times(2000, function () {
                return new TestJob(1);
            })->toArray()
        )
            ->then(function (Batch $batch) {
                $e = Redis::connection()->get('batch:' . $batch->id);

                logger(now() . 'finished ' . $e);

                User::query()
                    ->where('id', 1)
                    ->update([
                    'name' => now(),
                ]);
            })
            ->onConnection('redis')
            ->dispatch();

        exit;
        $e = Document::query()->with('documentable')->find(15);
        dd($e);
        exit;
        $dayNames = [];
        $startOfWeek = now()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $dayNames[] = $startOfWeek->copy()->addDays($i)->format('l');
        }

        dd($dayNames);
    }
}
