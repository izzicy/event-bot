<?php

namespace App\Services\Messages\Contracts;

use App\Services\Users\UserInterface;

interface UserMessageInterface
{
    /**
     * Get the user of this message.
     *
     * @return UserInterface
     */
    public function getUser();

    /**
     * Get the message of the user.
     *
     * @return string
     */
    public function getMessage();
}
