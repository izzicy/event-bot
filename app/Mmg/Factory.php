<?php

namespace App\Mmg;

use App\Mmg\Commands\AggregateCommand;
use App\Mmg\Contracts\CommandInterface;
use App\Mmg\Contracts\FactoryInterface;
use App\Mmg\Contracts\GameOperatorInterface;
use App\Mmg\Contracts\TesterInterface;
use App\Mmg\Testers\AggregateTester;

class Factory implements FactoryInterface
{
    /** @inheritDoc */
    public function createMessagesFromDiscord($messages)
    {
        return collect($messages)->map(function($message) {
            return new UserMessage($message);
        })->all();
    }

    /** @inheritDoc */
    public function createMineDistributer($picked, $mineCount): GameOperatorInterface
    {
        return new MineDistributer($picked, $mineCount);
    }

    /** @inheritDoc */
    public function createConquerer($picked, $users): GameOperatorInterface
    {
        return new Conquerer($picked, $users);
    }

    /** @inheritDoc */
    public function createAggregateTester($testers): TesterInterface
    {
        return new AggregateTester($testers);
    }

    /** @inheritDoc */
    public function createAggregateCommand($commands): CommandInterface
    {
        return new AggregateCommand($commands);
    }
}
