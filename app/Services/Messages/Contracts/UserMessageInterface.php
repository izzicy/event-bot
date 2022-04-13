<?php

namespace App\Services\Messages\Contracts;

use App\Services\Users\UserInterface;
use ImageEmbedInterface;

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

    /**
     * Get the embeds.
     *
     * @return ImageEmbedInterface[]
     */
    public function getImageAttachments();
}
