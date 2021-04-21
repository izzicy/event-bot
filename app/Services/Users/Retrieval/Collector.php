<?php

namespace App\Services\Users\Retrieval;

use App\Models\DiscordUser;
use App\Services\Users\DiscordUserCollection;

class Collector
{
    /** @var DiscordUser[] */
    private $users = [];

    /** @var Distributer */
    private $distributer;

    /**
     * @param Distributer $distributer
     */
    public function __construct(Distributer $distributer)
    {
        $this->distributer = $distributer;
    }

    /**
     * Add a new discord user.
     *
     * @param DiscordUser $user
     * @return void
     */
    public function addUser(DiscordUser $user)
    {
        $this->users[] = $user;
    }

    /**
     * Get the collected users.
     *
     * @return DiscordUserCollection
     */
    public function getUsers()
    {
        return new DiscordUserCollection($this->users);
    }

    /**
     * Unsubscribe the collector.
     *
     * @return void
     */
    public function unsubscribe()
    {
        $this->distributer->removeCollector($this);
    }
}
