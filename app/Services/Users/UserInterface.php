<?php

namespace App\Services\Users;

interface UserInterface
{
    /**
     * Get the user id.
     *
     * @return string
     */
    public function getId();

    /**
     * Get the username.
     *
     * @return string
     */
    public function getUsername();

    /**
     * Get the user's avatar url.
     *
     * @return string
     */
    public function getAvatar();
}
