<?php

namespace App\Discord;

use Discord\Discord;
use Illuminate\Support\Facades\Log;

abstract class DiscordSession
{
    /**
     * The discord api instance.
     *
     * @var Discord
     */
    protected $discord;

    /**
     * The session factory.
     *
     * @var SessionFactory
     */
    protected $sessionFactory;

    /**
     * Callbacks that are defined are stored here.
     * This is done so we can unsubscribe an event listener when we want to cleanup.
     *
     * @var \Closure[]
     */
    protected $callbacks = [];

    /**
     * Pass along the given discord client instance.
     *
     * @param Discord $discord
     * @return void
     */
    public function withDiscord($discord)
    {
        $this->discord = $discord;
    }

    /**
     * Pass along the given discord client instance.
     *
     * @param SessionFactory $discord
     * @return void
     */
    public function withSessionFactory($sessionFactory)
    {
        $this->sessionFactory = $sessionFactory;
    }

    /**
     * Start the session and run the discord client.
     *
     * @return void
     */
    public function open()
    {
        $this->initializing();
        $this->initialize();
    }

    /**
     * Start only the session, assume the discord client already has started.
     *
     * @return void
     */
    public function start()
    {
        $this->initializing();
        $this->initialized();

    }

    /**
     * Create a new discord session.
     *
     * @param string $sessionClass
     * @param array $parameters
     * @return DiscordSession
     */
    protected function createSession($sessionClass, $parameters = [])
    {
        return $this->sessionFactory->create($sessionClass, $this->discord, $parameters);
    }

    /**
     * Initialize the discord session.
     *
     * @return void
     */
    protected function initialize()
    {
        $this->discord->on('ready', $this->callback('initialized'));
        $this->discord->run();
    }

    /**
     * Before the discord client instance has started.
     *
     * @return void
     */
    protected function initializing()
    {
        // no-op
    }

    /**
     * After the discord client instance has started.
     *
     * @return void
     */
    protected function initialized()
    {
        // no-op
    }

    /**
     * Create a callback for the given method.
     *
     * @param string $method
     * @return array
     */
    protected function callback($method)
    {
        $arguments = array_slice(func_get_args(), 1);

        if (isset($this->callbacks[$method])) {
            return $this->callbacks[$method];
        }

        return $this->callbacks[$method] = function() use ($method, $arguments) {
            try {
                call_user_func_array([$this, $method], array_merge($arguments, func_get_args()));
            }
            catch (\Throwable $e) {
                throw $e;
            }
        };
    }
}
