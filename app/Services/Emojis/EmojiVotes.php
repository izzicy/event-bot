<?php

namespace App\Services\Emojis;

use App\Services\Votes\VotesResultsInterface;

class EmojiVotes implements VotesResultsInterface
{
    /**
     * The emoji interpreter.
     *
     * @var AbstractEmojiInterpreter
     */
    protected $interpreter;

    /**
     * The counted names of the emojis.
     *
     * @var array
     */
    protected $counts = [];

    /**
     * The counted votes.
     *
     * @var array
     */
    protected $countedVotes = [];

    /**
     * The emoji votes constructor.
     *
     * @param AbstractEmojiInterpreter $interpreter
     */
    public function __construct(AbstractEmojiInterpreter $interpreter)
    {
        $this->interpreter = $interpreter;
    }

    /**
     * Add the emoji to the votes.
     *
     * @param string $emoji
     * @param int $count
     * @return $this
     */
    public function addEmoji($emoji, $count)
    {
        $emoji = $this->interpreter->convertEmojiAliases($emoji);
        $name = $this->interpreter->toName($emoji);

        if ($name === null) {
            return $this;
        }

        if (empty($this->counts[$name])) {
            $this->counts[$name] = 0;
        }

        $this->counts[$name] += $count;

        return $this;
    }

    /**
     * Sort the votes.
     *
     * @return $this
     */
    public function sortVotes()
    {
        uasort($this->counts, function($a, $b) {
            return $b - $a;
        });

        $this->countedVotes = [];

        $previousCount = null;
        $currentIndex = -1;

        foreach ($this->counts as $name => $count) {
            if ($count !== $previousCount) {
                $currentIndex += 1;
            }

            $this->countedVotes[$currentIndex][] = $name;

            $previousCount = $count;
        }

        return $this;
    }

    /**
     * Get all votes at a specific place.
     * Starts at 0.
     *
     * @param int $place
     * @return array|null
     */
    public function getAllAtPlace($place)
    {
        return $this->countedVotes[$place];
    }

    /**
     * Get the first vote at the given place.
     *
     * @param int $place
     * @return string|null
     */
    public function getFirstAtPlace($place)
    {
        return $this->countedVotes[$place][0] ?? null;
    }

    /**
     * Get a random vote at the given place.
     *
     * @param int $place
     * @return string|null
     */
    public function getRandomAtPlace($place)
    {
        if (empty($this->countedVotes[$place])) {
            return null;
        }

        return collect($this->countedVotes[$place])->random();
    }
}
