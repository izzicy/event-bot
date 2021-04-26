<?php

namespace App\Services\Messages\Contracts;

use App\Services\Users\UserInterface;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\Member;
use Discord\Parts\User\User;

interface FactoryInterface
{
    /**
     * Create from the direct responses to the given user.
     *
     * @param Message[] $messagesInReverseOrder
     * @param Member|User|UserInterface|string $user
     * @return UserMessageInterface[]
     */
    public function createFromDirectResponses($messagesInReverseOrder, $user);

    /**
     * Create a new user message from discord.
     *
     * @param Message $message
     * @return UserMessageInterface
     */
    public function createMessageFromDiscord(Message $message): UserMessageInterface;
}
