<?php

namespace App\Discord;

use Discord\Discord;

abstract class DiscordSession
{
    /**
     * The discord api instance.
     *
     * @var Discord
     */
    protected $discord;

    /**
     * The session options.
     *
     * @var array
     */
    protected $options;

    /**
     * Construct a new discord session.
     *
     * @param Discord $discord
     * @param array $options
     */
    public function __construct($discord, $options)
    {
        $this->discord = $discord;
        $this->options = $options;
    }

    /**
     * Stop the discord session.
     *
     * @return void
     */
    public function stop()
    {
        $this->stop();
    }

    /**
     * Get the value of a command option.
     *
     * @param  string|null  $key
     * @return string|array|bool|null
     */
    protected function option($key = null)
    {
        if (is_null($key)) {
            return $this->options;
        }

        return $this->options[$key] ?? null;
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
     * Close the discord session.
     *
     * @return void
     */
    protected function close()
    {
        $this->discord->close();
        $this->closed();
    }

    /**
     * The discord session has initialized.
     *
     * @return void
     */
    protected function initialized()
    {
        //
    }


    /**
     * The discord session is closed.
     *
     * @return void
     */
    protected function closed()
    {
        //
    }

    /**
     * Create a callback for the given method.
     *
     * @param string $method
     * @return array
     */
    protected function callback($method)
    {
        return function() use ($method) {
            call_user_func_array([$this, $method], func_get_args());
        };
    }

    /**
     * Start the discord session.
     *
     * @return void
     */
    abstract public function start();
}
