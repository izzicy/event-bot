<?php

namespace App\Services\Messages;

use App\Services\Messages\Contracts\FactoryInterface;
use App\Services\Messages\Contracts\UserMessageInterface;
use App\Services\Users\UserInterface;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\Member;
use Discord\Parts\User\User;

class Factory implements FactoryInterface
{
    /** @inheritDoc */
    public function createFromDirectResponses($messagesInReverseOrder, $user)
    {
        $userMessages = [];
        $userId = $user;

        if ($user instanceof Member) {
            $userId = $user->user->id;
        } else if ($user instanceof User) {
            $userId = $user->id;
        } else if ($user instanceof UserInterface) {
            $userId = $user->getId();
        }

        foreach ($messagesInReverseOrder as $message) {
            $author = $message->author;

            if ($author instanceof Member && $author->user->id == $userId) {
                break;
            } else if ($author->id === $userId) {
                break;
            }

            array_unshift($userMessages, new DiscordUserMessage($message));
        }

        return $userMessages;
    }

    /** @inheritDoc */
    public function createMessageFromDiscord(Message $message): UserMessageInterface
    {
        return new DiscordUserMessage($message);
    }
}
