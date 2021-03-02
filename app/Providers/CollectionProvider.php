<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\ServiceProvider;

class CollectionProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Collection::macro('toItemsInSeries', function ($or = false) {
            if ($this->count() === 0) {
                return null;
            } else if ($this->count() === 1) {
                return $this->first();
            } else {
                $itemsWithoutLast = $this->values();
                $lastItem = $itemsWithoutLast->pop();

                return (
                    $itemsWithoutLast->join(Lang::get('grammar.items-in-series-punctuation'))
                    . (
                        $or
                        ? Lang::get('grammar.items-in-series-and')
                        : Lang::get('grammar.items-in-series-or')
                    ) . $lastItem
                );
            }
        });

    }
}
