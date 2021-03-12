<?php

namespace App\Providers;

use App\Services\MMGame\Factory;
use Illuminate\Support\ServiceProvider;

class MultiplayerMinesweeperProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Factory::class);
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
