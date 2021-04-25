<?php

namespace App\Services\Messages\Contracts;

use Discord\Parts\Channel\Message;

interface FactoryInterface
{
    /**
     * Create a new user message from discord.
     *
     * @param Message $message
     * @return UserMessageInterface
     */
    public function createMessageFromDiscord(Message $message): UserMessageInterface;
}
