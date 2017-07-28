<?php

namespace Arkade\RetailDirections\Modules;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Arkade\RetailDirections\Address;
use Arkade\RetailDirections\Customer;
use Arkade\RetailDirections\Exceptions;

class Customers extends AbstractModule
{
    /**
     * Return a single customer by ID.
     *
     * @param  string      $id
     * @param  Carbon|null $datetime
     * @return Customer
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

        $customer = Customer::fromXml($response->Customer);

        foreach ($response->Addresses->Address as $address) {
            $customer->getAddresses()->push(Address::fromXml($address));
        }

        return $customer;
    }

    /**
     * Return a collection of customers for an email address.
     *
     * @param  string      $email
     * @param  Carbon|null $datetime
     * @return Collection
     * @throws Exceptions\NotFoundException
     * @throws Exceptions\ServiceException
     */
    public function findByEmail($email, Carbon $datetime = null)
    {
        try {
            $response = $this->client->call('CustomerGetByEmail', [
                'CustomerGetByEmail' => [
                    'emailAddress' => $email,
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

        $collection = new Collection;

        foreach ($response->Customers->Customer as $customer) {
            $collection->push(Customer::fromXml($customer));
        }

        return $collection;
    }
}