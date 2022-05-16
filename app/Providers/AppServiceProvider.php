<?php

namespace App\Providers;

use App\Models\DiscordUser;
use App\Services\BillyGame\StateEmojiInterpreter;
use App\Services\BillyGame\VoteEmojiInterpreter;
use App\Services\BillyGame\VotesInterpreter;
use App\Services\Pipeline\PipelineFactory;
use App\Services\Pipeline\PromisePipeline;
use App\Services\Users\Retrieval\Collector;
use App\Services\Users\Retrieval\Distributer;
use App\Services\Users\Retrieval\RetrievedObserver;
use App\Services\Users\UserModelRepository;
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
        $this->app->singleton(UserModelRepository::class);
        $this->app->singleton(Distributer::class);

        $this->app->bind(Collector::class, function($app) {
            return $app[Distributer::class]->createCollector();
        });

        $this->app->singleton(\App\Contracts\Sprites\SpriteBuilderFactory::class, \App\Services\Sprites\SpriteBuilderFactory::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        DiscordUser::observe(RetrievedObserver::class);
    }
}
