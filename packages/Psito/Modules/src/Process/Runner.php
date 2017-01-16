<?php

namespace Psito\Modules\Process;

use Psito\Modules\Contracts\RunableInterface;
use Psito\Modules\Repository;

class Runner implements RunableInterface
{
    /**
     * The module instance.
     *
     * @var \Psito\Modules\Repository
     */
    protected $module;

    /**
     * The constructor.
     *
     * @param \Psito\Modules\Repository $module
     */
    public function __construct(Repository $module)
    {
        $this->module = $module;
    }

    /**
     * Run the given command.
     *
     * @param string $command
     */
    public function run($command)
    {
        passthru($command);
    }
}
