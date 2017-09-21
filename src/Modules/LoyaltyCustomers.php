<?php

namespace Arkade\RetailDirections\Modules;

use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Collection;
use Arkade\RetailDirections\Customer;
use Arkade\RetailDirections\Exceptions;
use Arkade\RetailDirections\LoyaltyCustomer;

class LoyaltyCustomers extends AbstractModule
{
    /**
     * Return a single customer by ID.
     *
     * @param  string      $id
     * @param  Carbon|null $datetime
     * @return LoyaltyCustomer
     * @throws Exceptions\NotFoundException
     * @throws Exceptions\ServiceException
     */
    public function findById($id, Carbon $datetime = null)
    {
        try {
            $response = $this->client->call('CustomerGet', [
                'CustomerGet' => [
                    'customerId'        => $id,
                    'requestDateTime'   => $this->client->formatDateTime($datetime ?: Carbon::now()),
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

        foreach ($response->LoyaltyCustomers->LoyaltyCustomer as $customer) {
            $collection->push(LoyaltyCustomer::fromXml($customer));
        }

        if ($collection->isEmpty()) {
            throw (new Exceptions\NotFoundException)
                ->setHistoryContainer($this->client->getHistoryContainer());
        }

        return $collection->first();
    }

    /**
     * Find a loyalty customer from their parent customer.
     *
     * @param Customer $customer
     * @param Carbon|null $datetime
     * @return LoyaltyCustomer
     */
    public function findByParent(Customer $customer, Carbon $datetime = null)
    {
        return $this->findById($customer->getId(), $datetime);
    }

    /**
     * Get a customers point balance.
     *
     * @param LoyaltyCustomer $customer
     * @return float
     * @throws Exceptions\ServiceException
     */
    public function getPointsBalance(LoyaltyCustomer $customer)
    {
        // Adjusting with '0' points gives the current points balance.
        return $this->handleUpdatePointsBalance($customer, 0);
    }

    /**
     * Update a customers point balance.
     *
     * @param LoyaltyCustomer $customer
     * @param $balance
     * @return float
     * @throws Exceptions\ServiceException
     */
    public function updatePointsBalance(LoyaltyCustomer $customer, $balance)
    {
        return $this->handleUpdatePointsBalance($customer, ($balance - $this->getPointsBalance($customer)));
    }

    /**
     * Handle updating a customers points balance.
     *
     * @param LoyaltyCustomer $customer
     * @param $balance
     * @return float
     */
    protected function handleUpdatePointsBalance(LoyaltyCustomer $customer, $balance)
    {
        if (! $customer->loyaltyGroupDescription || ! $customer->loyaltyCode) {
            throw new DomainException('Customer loyaltyGroupDescription and loyaltyCode are required to update points balance. Try getting a fresh copy of the customer first via findByParent() or findById().');
        }

        $response = $this->client->call('LoyaltyCustomerPointsAdjust', [
            'customerPointsList' => [
                'LoyaltyPointsAdjust' => [
                    'customerID'        => $customer->getId(),
                    'LoyaltyGroupCode'  => $customer->loyaltyGroupDescription,
                    'LoyaltyCode'       => $customer->loyaltyCode,
                    'PointToAdjust'     => $balance
                ]
            ]
        ]);

        return (float) $response->customerPointsList->LoyaltyPointsAdjust->PointsBalance;
    }

    /**
     * Create or update a customer.
     *
     * The API creates or updates the customer for us (if an ID is provided).
     *
     * @param LoyaltyCustomer $customer
     * @return Customer|LoyaltyCustomer|string
     */
    public function createOrUpdate(LoyaltyCustomer $customer)
    {
        if (! $customer->getId()) {
            return $this->create($customer);
        }

        return $this->update($customer);
    }

    /**
     * Create the customer.
     *
     * @param Customer|LoyaltyCustomer $customer
     * @return LoyaltyCustomer|string
     */
    public function create(LoyaltyCustomer $customer)
    {
        return $this->persist($customer);
    }

    /**
     * Update provided customer.
     *
     * @param  LoyaltyCustomer  $customer
     * @return Customer
     * @throws Exceptions\NotFoundException
     * @throws Exceptions\ServiceException
     */
    public function update(LoyaltyCustomer $customer)
    {
        if (! $customer->getId()) {
            throw new DomainException('You must provide an ID when updating a customer. Try using findById() or findByParent() first.');
        }

        return $this->persist($customer);
    }

    /**
     * Persist the customer.
     *
     * @param LoyaltyCustomer $customer
     * @return LoyaltyCustomer
     * @throws Exceptions\AlreadyExistsException
     * @throws Exceptions\ServiceException
     */
    protected function persist(LoyaltyCustomer $customer)
    {
        $response = $this->client->call('LoyaltyCustomerEdit', [
            'LoyaltyCustomerEdit' => $customer->toArray()
        ]);

        return LoyaltyCustomer::fromXml($response->LoyaltyCustomerEdit);
    }
}
