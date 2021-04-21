<?php

namespace App\Services\Users\Retrieval;

use App\Models\DiscordUser;

class RetrievedObserver
{
    /**
     * Handle the user retrieval event.
     *
     * @param DiscordUser $user
     * @return void
     */
    public function retrieved(DiscordUser $user)
    {
        /** @var Distributer */
        $distributer = app(Distributer::class);

        $distributer->addUser($user);
    }
}
