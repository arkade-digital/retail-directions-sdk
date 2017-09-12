<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Fluent;
use Illuminate\Support\ServiceProvider;

class LaravelServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function ($app)
        {
            $client = new Client(config('services.retaildirections.wsdl'));

            if ($credentials = $this->resolveCredentialsFromConfig()) {
                $client->setCredentials($credentials);
            }

            if (config('services.retaildirections.location')) {
                $client->setLocation(new Location(config('services.retaildirections.location')));
            }

            $historyContainer = new HistoryContainer;

            $this->setupRecorder($historyContainer);

            $client->setHistoryContainer($historyContainer);

            return $client;
        });
    }

    /**
     * Resolve client credentials from services config.
     *
     * @return Credentials|null
     */
    protected function resolveCredentialsFromConfig()
    {
        $username = config('services.retaildirections.username');
        $password = config('services.retaildirections.password');

        if ($username && $password) {
            return new Credentials($username, $password);
        }

        return null;
    }

    /**
     * Setup recorder middleware if the HttpRecorder plugin is bound.
     *
     * @param  HistoryContainer $historyContainer
     * @return HistoryContainer
     */
    protected function setupRecorder(HistoryContainer $historyContainer)
    {
        if (! $this->app->bound('Omneo\Plugins\HttpRecorder\Recorder')) {
            return $historyContainer;
        }

        $recorder = $this->app->make('Omneo\Plugins\HttpRecorder\Recorder');

        $historyContainer->push(function (Fluent $transaction) use ($recorder)
        {
            // Request could not be processed, we can't record this
            if (! $transaction->request)
            {
                $this->app->make('Psr\Log\LoggerInterface')->error(
                    'Could not process Retail Directions request for recorder',
                    ['headers' => $transaction->requestHeaders, 'body' => $transaction->requestBody]
                );

                return;
            }

            $recorder->record(
                $transaction->request,
                $transaction->response,
                $transaction->exception,
                ['retail-directions', 'outgoing']
            );
        });

        return $historyContainer;
    }
}
