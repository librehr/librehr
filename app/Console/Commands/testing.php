<?php

namespace App\Console\Commands;

use App\Models\Document;
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
