<?php

namespace App\Providers;

use App\Services\ChooseYourDoorGame\ChoiceEmojiInterpreter;
use App\Services\ChooseYourDoorGame\DoorImageCreator;
use App\Services\ChooseYourDoorGame\PhraseCreator;
use App\Services\ChooseYourDoorGame\ResultsImageCreator;
use Illuminate\Support\ServiceProvider;

class ChooseYourDoorProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ChoiceEmojiInterpreter::class);
        $this->app->singleton(ResultsImageCreator::class);
        $this->app->singleton(DoorImageCreator::class);
        $this->app->singleton(PhraseCreator::class);
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
