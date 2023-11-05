<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AfterFifteenMinutesCommand extends Command
{
    protected $signature = 'hunters:after_15_min {time}';
    protected $description = 'Hunters send to online-hunter';

    public function handle()
    {
        $time = $this->argument('time');
    }
}
