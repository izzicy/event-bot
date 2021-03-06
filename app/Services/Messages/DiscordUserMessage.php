<?php

namespace App\Services\Messages;

use App\Services\Messages\Contracts\UserMessageInterface;
use App\Services\Users\DiscordUser;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\Member;

class DiscordUserMessage implements UserMessageInterface
{
    /** @var Message */
    protected $message;

    /**
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /** @inheritDoc */
    public function getUser()
    {
        $author = $this->message->author;

        return new DiscordUser(($author instanceof Member) ? $author->user : $author);
    }

    /** @inheritDoc */
    public function getMessage()
    {
        return $this->message->content;
    }
}
