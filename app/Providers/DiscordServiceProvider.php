<?php

namespace App\Providers;

use App\Services\Messages\Contracts\FactoryInterface;
use App\Services\Messages\Factory;
use Illuminate\Support\ServiceProvider;

class DiscordServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FactoryInterface::class, Factory::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
