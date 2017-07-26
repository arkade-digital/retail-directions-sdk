<?php

namespace Arkade\RetailDirections\Modules;

use Carbon\Carbon;
use Arkade\RetailDirections\Exceptions;
use Mockery\Exception;

class Customers extends AbstractModule
{
    /**
     * Return a single customer by ID.
     *
     * @param  string      $id
     * @param  Carbon|null $datetime
     * @return Entities\Customer
     * @throws Exceptions\NotFoundException
     * @throws Exceptions\ServiceException
     */
    public function findById($id, Carbon $datetime = null)
    {
        try {
            $response = $this->client->call('CustomerGet', [
                'CustomerGet' => [
                    'customerId' => $id,
                    'requestDateTime' => $this->client->formatDateTime($datetime ?: Carbon::now())
                ]
            ]);
        } catch (Exceptions\ServiceException $e) {

            if (60103 == $e->getCode()) {
                throw (new Exceptions\NotFoundException)
                    ->setHistoryContainer($e->getHistoryContainer());
            }

            throw $e;

        }
    }

    /**
     * Find a customer by search attributes.
     *
     * @param  array $searchAttributes
     * @return Entities\Customer
     */
    public function create(array $searchAttributes)
    {
        $response = $this->client->call('CustomerFind', [
            'CustomerFind' => $searchAttributes
        ]);
    }
}