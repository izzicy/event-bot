<?php

namespace App\Providers;

use App\Mgg\GameRepository;
use App\Mmg\Contracts\FactoryInterface;
use App\Mmg\Factory;
use Illuminate\Support\ServiceProvider;

class MmgServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FactoryInterface::class, Factory::class);
        $this->app->singleton(GameRepository::class);
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
