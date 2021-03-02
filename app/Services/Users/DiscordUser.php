<?php

namespace App\Services\Users;

use Discord\Parts\User\User;

class DiscordUser implements UserInterface
{
    /**
     * The discord user.
     *
     * @var User
     */
    protected $user;

    /**
     * Discord user constructor.
     *
     * @param User $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->user->id;
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->user->username;
    }

    /**
     * @inheritdoc
     */
    public function getAvatar()
    {
        return $this->user->avatar;
    }
}
