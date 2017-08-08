<?php

namespace Arkade\RetailDirections;

use Closure;
use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;

class HistoryContainer extends Collection
{
    /**
     * Array of closures to call when new transaction pushed.
     *
     * @var array[Closure]
     */
    protected $middleware = [];

    /**
     * Record a new transaction into the container.
     *
     * @param  Fluent $transaction
     * @return HistoryContainer
     */
    public function record(Fluent $transaction)
    {
        $this->push($transaction);

        $this->fireMiddleware($transaction);

        return $this;
    }

    /**
     * Push a middleware into the collection.
     *
     * @param  Closure $middleware
     * @return HistoryContainer
     */
    public function pushMiddleware(Closure $middleware)
    {
        array_push($this->middleware, $middleware);

        return $this;
    }

    /**
     * Fire registered middleware for the transaction.
     *
     * @param  Fluent $transaction
     */
    protected function fireMiddleware(Fluent $transaction)
    {
        foreach ($this->middleware as $middleware) {
            $middleware($transaction);
        }
    }
}
