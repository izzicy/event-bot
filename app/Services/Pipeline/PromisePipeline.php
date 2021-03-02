<?php

namespace App\Services\Pipeline;

use Illuminate\Pipeline\Pipeline;
use React\Promise\Promise;

class PromisePipeline extends Pipeline
{
    /**
     * @inheritdoc
     */
    public function through($promises)
    {
        $pipeline = [];

        foreach ($promises as $promise) {
            if (is_callable($promise)) {
                $pipeline[] = function($passable, $next) use ($promise) {
                    $return = $promise($passable, $next);

                    if ($return instanceof Promise) {
                        $return->done(function($resolved) use ($next) {
                            $next($resolved);
                        });
                    }
                };
            } else if (is_string($promise)) {
                $pipeline[] = $promise;
            } else {
                $pipeline[] = function($passable, $next) use ($promise) {
                    $promise->done(function($resolved) use ($next) {
                        $next($resolved);
                    });
                };
            }
        }

        return parent::through($pipeline);
    }
}
