<?php

namespace App\Services\Users;

use App\Services\Pipeline\PromisePipeline;
use Discord\Discord;
use Illuminate\Database\Eloquent\Collection;
use React\Promise\Deferred;
use React\Promise\Promise;

class DiscordUserCollection extends Collection
{
    /**
     * Load all discord users.
     *
     * @param Discord $discord
     * @return Promise
     */
    public function loadUsers(Discord $discord)
    {
        $deferred = new Deferred();

        $pipeline = $this->map(function($user) use ($discord) {
            return $user->loadUser($discord);
        })->all();

        app(PromisePipeline::class)
            ->through($pipeline)
            ->then(function() use ($deferred) {
                $deferred->resolve();
            });

        return $deferred->promise();
    }
}
