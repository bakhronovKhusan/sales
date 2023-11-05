<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BeforeThirtyMinutesCommand extends Command
{
    protected $signature = 'hunters:before_30_min {time}';
    protected $description = 'Hunters register students';

    public function handle()
    {
        $time = $this->argument('time');

    }
}
