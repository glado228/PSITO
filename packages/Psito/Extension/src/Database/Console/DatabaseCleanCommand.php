<?php

namespace Psito\Extension\Database\Console;

use Jumilla\Versionia\Laravel\Console\DatabaseCleanCommand as BaseCommand;

class DatabaseCleanCommand extends BaseCommand
{
    /**
     * Create a new console command instance.
     */
    public function __construct()
    {
        $this->description = '[+] '.$this->description;

        parent::__construct();
    }
}
