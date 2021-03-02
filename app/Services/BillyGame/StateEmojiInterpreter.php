<?php

namespace App\Services\BillyGame;

use App\Services\Emojis\AbstractEmojiInterpreter;

class StateEmojiInterpreter extends AbstractEmojiInterpreter
{
    /**
     * @inheritdoc
     */
    public function convertEmojiAliases($string)
    {
        $emojisWithAliases = config('billy-game.state-emojis-aliases');

        foreach ($emojisWithAliases as $emoji => $aliases) {
            foreach ($aliases as $alias) {
                $string = str_replace($alias, $emoji, $string);
            }
        }

        return $string;
    }

    /**
     * @inheritdoc
     */
    protected function getAssociatedEmojis()
    {
        return config('billy-game.state-emojis');
    }
}
