<?php

namespace App\Services\ChooseYourDoorGame;

use App\Services\Emojis\AbstractEmojiInterpreter;

class ChoiceEmojiInterpreter extends AbstractEmojiInterpreter
{
    /**
     * @inheritdoc
     */
    protected function getAssociatedEmojis()
    {
        return config('choose-your-door-game.vote-emojis');
    }
}
