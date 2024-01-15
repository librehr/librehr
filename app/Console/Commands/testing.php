<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
        $dayNames = [];
        $startOfWeek = now()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $dayNames[] = $startOfWeek->copy()->addDays($i)->format('l');
        }

        dd($dayNames);
    }
}
