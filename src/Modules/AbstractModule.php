<?php

namespace Arkade\RetailDirections\Modules;

use Arkade\RetailDirections\Client;

abstract class AbstractModule
{
    /**
     * Retail Directions SOAP client.
     *
     * @var Client
     */
    protected $client;

    /**
     * AbstractModule constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}