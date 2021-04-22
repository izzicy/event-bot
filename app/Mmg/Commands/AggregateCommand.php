<?php

namespace App\Mmg\Commands;

use App\Mmg\Contracts\CommandInterface;

class AggregateCommand implements CommandInterface
{
    /** @var CommandInterface[] */
    protected $commands;

    /**
     * @param CommandInterface[] $commands
     */
    public function __construct($commands)
    {
        $this->commands = $commands;
    }

    /** @inheritDoc */
    public function handleMessage($message)
    {
        foreach ($this->commands as $command) {
            $command->handleMessage($message);
        }
    }

    /** @inheritDoc */
    public function operateGame($game)
    {
        foreach ($this->commands as $command) {
            $command->operateGame($game);
        }
    }
}
