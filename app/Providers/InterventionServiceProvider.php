<?php

namespace App\Providers;

use App\Util\Intervention\ColorizeFromImageFilter;
use Illuminate\Support\ServiceProvider;

class InterventionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ColorizeFromImageFilter::class, function($app, $params) {
            return new ColorizeFromImageFilter($params['image'], $params['strength'] ?? 40);
        });
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
