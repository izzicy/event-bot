<?php

namespace App\Services\Users\Retrieval;

use App\Models\DiscordUser;

class Distributer
{
    /** @var Collector[] */
    private $collectors = [];

    /**
     * Add a user.
     *
     * @param DiscordUser $user
     * @return void
     */
    public function addUser($user)
    {
        foreach ($this->collectors as $collector) {
            $collector->addUser($user);
        }
    }

    /**
     * Create a new collector.
     *
     * @return Collector
     */
    public function createCollector()
    {
        $collector = new Collector($this);

        $this->collectors[] = $collector;

        return $collector;
    }

    /**
     * Remove the collector.
     *
     * @param Collector $collector
     * @return void
     */
    public function removeCollector(Collector $collector)
    {
        $this->collectors = collect($this->collectors)->filter(function($c) use ($collector) {
            return $c !== $collector;
        })->all();
    }
}
