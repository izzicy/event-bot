<?php

namespace App\Discord;

use Closure;
use Discord\Discord;

/**
 * @deprecated
 */
class OnDiscordReadyMiddleware
{
    /**
     * The discord instance.
     *
     * @var Discord
     */
    protected $discord;

    /**
     * Middleware constructor.
     *
     * @param Discord $discord
     */
    public function __construct(Discord $discord)
    {
        $this->discord = $discord;
    }

    /**
     * Handle the pipeline.
     *
     * @param mixed $passable
     * @param Closure $next
     * @return void
     */
    public function handle($passable, Closure $next)
    {
        $this->discord->on('ready', function() use ($passable, $next) {
            $next($passable);
        });
    }
}
