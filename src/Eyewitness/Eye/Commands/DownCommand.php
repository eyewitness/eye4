<?php

namespace Eyewitness\Eye\Commands;

use Illuminate\Foundation\Console\DownCommand as OriginalDownCommand;
use Eyewitness\Eye\Eye;

class DownCommand extends OriginalDownCommand
{
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        parent::fire();

        app(Eye::class)->api()->down();
    }
}
