<?php

namespace App\Services\BillyGame;

use App\Services\Emojis\AbstractEmojiInterpreter;

class VoteEmojiInterpreter extends AbstractEmojiInterpreter
{
    /**
     * @inheritdoc
     */
    protected function getAssociatedEmojis()
    {
        return config('billy-game.vote-emojis');
    }
}
