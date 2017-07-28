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

    /**
     * Create provided customer.
     *
     * @param  Customer $customer
     * @param  Carbon   $datetime Optional datetime for findById request
     * @return Customer
     * @throws Exceptions\AlreadyExistsException
     * @throws Exceptions\ValidationException
     * @throws Exceptions\ServiceException
     */
    public function create(Customer $customer, Carbon $datetime = null)
    {
        // Throw AlreadyExistsException if customer already exists
        try {
            if ($this->findById($customer->getId(), $datetime)) {
                throw new Exceptions\AlreadyExistsException;
            }
        } catch (Exceptions\NotFoundException $e) {}

        return $this->createOrUpdate($customer);
    }

    /**
     * Update provided customer.
     *
     * @param  Customer $customer
     * @param  Carbon   $datetime Optional datetime for findById request
     * @return Customer
     * @throws Exceptions\NotFoundException
     * @throws Exceptions\AlreadyExistsException
     * @throws Exceptions\ValidationException
     * @throws Exceptions\ServiceException
     */
    public function update(Customer $customer, Carbon $datetime = null)
    {
        // Throw NotFoundException if customer does not exist
        $this->findById($customer->getId(), $datetime);

        return $this->createOrUpdate($customer);
    }

    /**
     * Create or update the provided customer.
     *
     * @param  Customer $customer
     * @return Customer
     * @throws Exceptions\AlreadyExistsException
     * @throws Exceptions\ValidationException
     * @throws Exceptions\ServiceException
     */
    public function createOrUpdate(Customer $customer)
    {
        return $this->createOrUpdateFromAttributes(array_merge(
            $customer->getAttributes(),
            ['customerId' => $customer->getId()]
        ));
    }

    /**
     * Create or update a customer from provided attributes.
     *
     * If the `customerId` attribute is provided, this will cause an update.
     *
     * @param  array $attributes
     * @return Customer
     * @throws Exceptions\AlreadyExistsException
     * @throws Exceptions\ValidationException
     * @throws Exceptions\ServiceException
     */
    public function createOrUpdateFromAttributes(array $attributes)
    {
        try {
            $response = $this->client->call('CustomerEdit', [
                'Customer' => $attributes
            ]);
        } catch (Exceptions\ServiceException $e) {

            if (58104 == $e->getCode()) {
                throw (new Exceptions\AlreadyExistsException)
                    ->setHistoryContainer($e->getHistoryContainer());
            }

            if (58110 == $e->getCode()) {
                throw (new Exceptions\ValidationException)
                    ->setHistoryContainer($e->getHistoryContainer());
            }

            throw $e;

        }

        return Customer::fromXml($response->Customer);
    }
}