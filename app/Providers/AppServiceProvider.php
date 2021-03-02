<?php

namespace App\Providers;

use App\Services\BillyGame\StateEmojiInterpreter;
use App\Services\BillyGame\VoteEmojiInterpreter;
use App\Services\BillyGame\VotesInterpreter;
use App\Services\Pipeline\PipelineFactory;
use App\Services\Pipeline\PromisePipeline;
use EmojiView;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(EmojiView::class);
        $this->app->singleton(StateEmojiInterpreter::class);
        $this->app->singleton(VoteEmojiInterpreter::class);
        $this->app->singleton(VotesInterpreter::class);
        $this->app->bind(PromisePipeline::class, function($app) {
            return new PromisePipeline($app);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
