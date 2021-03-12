<?php

namespace App\Models;

use App\Services\Users\DiscordUser as UserFromDiscord;
use App\Services\Users\DiscordUserCollection;
use App\Services\Users\HasUserTrait;
use App\Services\Users\UserInterface;
use Discord\Discord;
use Illuminate\Database\Eloquent\Model;
use React\Promise\Promise;
use function React\Promise\resolve as Resolve;

class DiscordUser extends Model implements UserInterface
{
    use HasUserTrait;

    /**
     * Load the user if they're missing.
     *
     * @param Discord $discord
     * @return Promise
     */
    public function loadUserIfMissing(Discord $discord)
    {
        if ($this->user !== null) {
            return Resolve($this->user);
        }

        return $this->loadUser($discord);
    }

    /**
     * Load the given user.
     *
     * @param Discord $discord
     * @return Promise
     */
    public function loadUser(Discord $discord)
    {
        return $discord->users->fetch($this->getKey())->then(function ($user) {
            $user = new UserFromDiscord($user);

            $this->user = $user;

            return $user;
        });
    }

    /**
     * @inheritdoc
     */
    public function newCollection(array $models = [])
    {
        return new DiscordUserCollection($models);
    }
}
