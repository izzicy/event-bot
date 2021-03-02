<?php

namespace App\Services\Choices;

use App\Services\Users\UserInterface;

interface ChoicesResultsInterface
{
    /**
     * Get the users who have chosen.
     *
     * @return UserInterface[]
     */
    public function getUsers();

    /**
     * Get the choice of the given user.
     *
     * @param UserInterface $user
     * @return string|null
     */
    public function getChoiceOfUser(UserInterface $user);
}
