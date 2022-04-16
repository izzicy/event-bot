<?php

namespace App\Discord;

use Discord\Discord;
use Illuminate\Contracts\Container\Container;

class SessionFactory
{
    /**
     * The container implementation.
     *
     * @var Container
     */
    protected $con;

    /**
     * Session factory contructor.
     *
     * @param Container $con
     */
    public function __construct(Container $con)
    {
        $this->con = $con;
    }

    /**
     * Construct a new session instance.
     *
     * @param string $sessionClass
     * @param Discord $discord
     * @param array $parameters
     * @return DiscordSession
     */
    public function create($sessionClass, $discord, $parameters = [])
    {
        $session = $this->con->make($sessionClass, $parameters);

        $session->withDiscord($discord);
        $session->withSessionFactory($this);

        return $session;
    }
}
