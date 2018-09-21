<?php

namespace Arkade\RetailDirections\Modules;

use Arkade\RetailDirections\LineItem;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Collection;
use Arkade\RetailDirections\Address;
use Arkade\RetailDirections\Order;
use Arkade\RetailDirections\Exceptions;
use Arkade\RetailDirections\Identification;

class Orders extends AbstractModule
{
    /**
     * Return a single Order by ID.
     *
     * @param  string      $id
     * @param  string      $customerId
     * @param  Carbon|null $datetime
     * @return Customer
     * @throws Exceptions\NotFoundException
     * @throws Exceptions\ServiceException
     */
    public function findById($id, $customerId, Carbon $datetime = null)
    {
        try {
            $response = $this->client->call('SalesOrderGet', [
                'SalesOrderGet' => [
                    'salesOrderCode' => $id,
                    'customerId' => $customerId,
                ]
            ]);
        } catch (Exceptions\ServiceException $e) {

            if (60103 == $e->getCode()) {
                throw (new Exceptions\NotFoundException)
                    ->setHistoryContainer($e->getHistoryContainer());
            }

            throw $e;

        }

        return Order::fromXml(
            $response->Customer,
            $response->CustomerIdentifications,
            $response->Addresses
        );
    }

    /**
     * Create provided customer.
     *
     * @param  Order $order
     * @param  Carbon   $datetime Optional datetime for findById request
     * @return Order
     * @throws Exceptions\AlreadyExistsException
     * @throws Exceptions\ValidationException
     * @throws Exceptions\ServiceException
     */
    public function create(Order $order, Carbon $datetime = null)
    {
        if ($order->getId()) {
            try {
                if ($this->findById($order->getId(), $datetime)) {
                    throw new Exceptions\AlreadyExistsException;
                }
            } catch (Exceptions\NotFoundException $e) { }
        }

        return $this->persist($order);
    }

    /**
     * Update provided customer.
     *
     * @param  Customer $customer
     * @param  Carbon   $datetime Optional datetime for findById request
     * @return Order
     * @throws Exceptions\NotFoundException
     * @throws Exceptions\AlreadyExistsException
     * @throws Exceptions\ValidationException
     * @throws Exceptions\ServiceException
     */
    public function update(Order $order, Carbon $datetime = null)
    {
        if (! $customer->getId()) {
            throw new DomainException('You must provide an ID when updating a order.');
        }

        $this->findById($order->getId(), $datetime);

        return $this->persist($order);
    }

    /**
     * Create or update the provided customer entity.
     *
     * @param  Order $order
     * @return Order
     * @throws Exceptions\AlreadyExistsException
     * @throws Exceptions\ValidationException
     * @throws Exceptions\ServiceException
     */
    protected function persist(Order $order)
    {
        $payload = ['SalesOrderDetail' => $order->getAttributes()];

        if ($order->getId()) {
            $payload['SalesOrderDetail']['salesOrderCode'] = $order->getId();
        }

	    if ($order->getLineItems()->count()) {
		    $payload['SalesOrderLines'] = $order->getLineItems()->map(function(LineItem $item) {
			    return $item->getXmlArray();
		    })->toArray();
	    }

        try {
            $response = $this->client->call('SalesOrderSubmit', $payload);
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

	    return Order::fromXml(
            $response->SalesOrderDetail,
            $response->SalesOrderLines
        );
    }
}