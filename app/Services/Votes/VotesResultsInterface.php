<?php

namespace App\Services\Votes;

interface VotesResultsInterface
{
    /**
     * Get all of the voted items at a specific place.
     * Starts at 0.
     *
     * @param int $place
     * @return array|null
     */
    public function getAllAtPlace($place);

    /**
     * Get the first voted item at the given place.
     *
     * @param int $place
     * @return string|null
     */
    public function getFirstAtPlace($place);

    /**
     * Get a random voted item from the given place.
     *
     * @param int $place
     * @return string|null
     */
    public function getRandomAtPlace($place);
}
