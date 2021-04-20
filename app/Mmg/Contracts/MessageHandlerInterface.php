<?php

namespace App\Mmg\Contracts;

interface MessageHandlerInterface
{
    /**
     * Handle the message.
     *
     * @param UserMessageInterface $message
     * @return void
     */
    public function handleMessage($message);
}
