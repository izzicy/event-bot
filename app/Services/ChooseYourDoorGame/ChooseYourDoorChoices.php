<?php

namespace App\Services\ChooseYourDoorGame;

use App\Services\Choices\ChoicesResultsInterface;
use App\Services\Users\DiscordUser;
use App\Services\Users\UserInterface;
use Discord\Parts\User\User;

class ChooseYourDoorChoices implements ChoicesResultsInterface
{
    /**
     * A list of users.
     *
     * @var User[]
     */
    protected $users = [];

    /**
     * A list of user choices by discord id.
     *
     * @var string[]
     */
    protected $choiceByUserId = [];

    /**
     * Add a user with the given emoji
     *
     * @param User $user
     * @param string $emoji
     * @return $this
     */
    public function addUserWithEmoji(User $user, $emoji)
    {
        if (isset($this->choiceByUserId[$user->id])) {
            $this->choiceByUserId[$user->id] = 'cheater';
        } else {
            $name = app(ChoiceEmojiInterpreter::class)->toName($emoji);

            $this->users[] = $user;
            $this->choiceByUserId[$user->id] = $name;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUsers()
    {
        return array_map(function($user) {
            return new DiscordUser($user);
        }, $this->users);
    }

    /**
     * @inheritdoc
     */
    public function getChoiceOfUser(UserInterface $user)
    {
        return $this->choiceByUserId[$user->getId()] ?? null;
    }
}
