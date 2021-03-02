<?php

namespace App\Services\Users;

use Exception;

trait HasUserTrait
{
    /**
     * The user instance.
     *
     * @var UserInterface|null
     */
    protected $user;

    /**
     * Get the user id.
     *
     * @return string
     */
    public function getId()
    {
        if ($this->user === null) {
            throw new Exception('Missing user');
        }

        return $this->user->getId();
    }

    /**
     * Get the username.
     *
     * @return string
     */
    public function getUsername()
    {
        if ($this->user === null) {
            throw new Exception('Missing user');
        }

        return $this->user->getUsername();
    }

    /**
     * Get the user's avatar url.
     *
     * @return string
     */
    public function getAvatar()
    {
        if ($this->user === null) {
            throw new Exception('Missing user');
        }

        return $this->user->getAvatar();
    }
}
