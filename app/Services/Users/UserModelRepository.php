<?php

namespace App\Services\Users;

use App\Models\DiscordUser;

class UserModelRepository
{
    /**
     * Retrieve a user from the database with the give instance.
     * Creates a model user if one doesn't already exist.
     *
     * @param UserInterface $user
     * @return DiscordUser
     */
    public function retrieveFromInstance(UserInterface $user)
    {
        if ($discordUser = DiscordUser::find($user->getId())) {
            return $discordUser;
        }

        $discordUser = DiscordUser::forceCreate([
            'id' => $discordUser->getId(),
        ]);
    }
}
