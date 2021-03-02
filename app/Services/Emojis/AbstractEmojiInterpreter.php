<?php

namespace App\Services\Emojis;

abstract class AbstractEmojiInterpreter
{
    /**
     * Check whether this interpreter possesses over this emoiji.
     *
     * @param string $emoji
     * @return boolean
     */
    public function hasEmoji($emoji)
    {
        return empty(array_flip($this->getAssociatedEmojis())[$emoji]) === false;
    }

    /**
     * Convert the emoji to a name.
     *
     * @param string $emoji
     * @return string|null
     */
    public function toName($emoji)
    {
        return array_flip($this->getAssociatedEmojis())[$emoji] ?? null;
    }

    /**
     * Convert the command to an emoji.
     *
     * @param string $emoji
     * @return string|null
     */
    public function toEmoji($command)
    {
        return $this->getAssociatedEmojis()[$command] ?? null;
    }

    /**
     * Get a list of all emojis.
     *
     * @return array
     */
    public function getEmojis()
    {
        return array_values($this->getAssociatedEmojis());
    }

    /**
     * Get a list of all names.
     *
     * @return array
     */
    public function getNames()
    {
        return array_keys($this->getAssociatedEmojis());
    }

    /**
     * Convert the emoji aliases in the given string.
     * This method may be overridden.
     *
     * @param string $string
     * @return string
     */
    public function convertEmojiAliases($string)
    {
        return $string;
    }

    /**
     * Get an associated array with the emojis.
     * The names are the keys and the emojis are the values.
     *
     * @return array
     */
    abstract protected function getAssociatedEmojis();
}
