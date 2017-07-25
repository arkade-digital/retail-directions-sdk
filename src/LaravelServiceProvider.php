<?php

namespace Arkade\RetailDirections;

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
}
