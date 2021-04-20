<?php

namespace App\Mmg\Contracts;

use App\Services\Users\DiscordUser;

interface UserMessageInterface
{
    /**
     * Get the user of this message.
     *
     * @return DiscordUser
     */
    public function getUser();

    /**
     * Get the message of the user.
     *
     * @return string
     */
    public function getMessage();
}
