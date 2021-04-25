<?php

namespace App\Providers;

use App\Zdg\Contracts\FactoryInterface;
use App\Zdg\Factory;
use Illuminate\Support\ServiceProvider;

class ZdgServiceProvider extends ServiceProvider
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
