<?php

namespace App\Mmg\Contracts;

use App\Services\Users\UserInterface;
use Discord\Parts\Channel\Message;

interface FactoryInterface
{
    /**
     * Create normalized messages from discord messages.
     *
     * @param Message[] $message
     * @return UserMessageInterface[]
     */
    public function createMessagesFromDiscord($messages);

    /**
     * Create a mine distributer.
     *
     * @param array[]int[] $picked
     * @param int $mineCount
     * @return GameOperatorInterface
     */
    public function createMineDistributer($picked, $mineCount): GameOperatorInterface;

    /**
     * Create the conquerer.
     *
     * @param array[]array[]int[] $picked
     * @param UserInterface[] $users
     * @return GameOperatorInterface
     */
    public function createConquerer($picked, $users): GameOperatorInterface;

    /**
     * Create an aggregate tester.
     *
     * @param TesterInterface[] $testers
     * @return TesterInterface
     */
    public function createAggregateTester($testers): TesterInterface;

    /**
     * Create an aggregate command.
     *
     * @param CommandInterface[] $commands
     * @return CommandInterface
     */
    public function createAggregateCommand($commands): CommandInterface;
}
