<?php

namespace App\Services\Messages;

use App\Services\Messages\Contracts\FactoryInterface;
use App\Services\Messages\Contracts\UserMessageInterface;
use Discord\Parts\Channel\Message;

class Factory implements FactoryInterface
{
    /** @inheritDoc */
    public function createMessageFromDiscord(Message $message): UserMessageInterface
    {
        return new DiscordUserMessage($message);
    }
}
