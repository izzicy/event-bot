<?php

namespace App\Models;

use App\Services\Users\DiscordUserCollection;
use App\Services\Users\HasUserTrait;
use App\Services\Users\UserInterface;
use Discord\Discord;
use Illuminate\Database\Eloquent\Model;
use React\Promise\Promise;

class DiscordUser extends Model implements UserInterface
{
    use HasUserTrait;

    /**
     * Load the given user.
     *
     * @param Discord $discord
     * @return Promise
     */
    public function loadUser(Discord $discord)
    {
        return $discord->users->fetch($this->getKey())->then(function ($user) {
            $user = new DiscordUser($user);

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
