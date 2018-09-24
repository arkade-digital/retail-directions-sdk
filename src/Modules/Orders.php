<?php

namespace Arkade\RetailDirections\Modules;

use Arkade\RetailDirections\LineItem;
use Arkade\RetailDirections\PaymentDetail;
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
     * Finalise provided order.
     *
     * @param Order $order
     * @param PaymentDetail[]|Collection $payments
     *
     * @return Order
     */
    public function finalise(Order $order, $payments)
    {
	    $payload = [
	    	'ConfirmationDetail' => [
	    		'salesorderCode' => $order->getId(),
	    		'action' => 'Approve',
		    ],
	    ];

	    // Add payments to order is available, this is the last chance below the order is locked
	    if ($payments->count()) {
		    $payload['PaymentDetails'] = $payments->map(function(PaymentDetail $payment) {
			    return $payment->getXmlArray();
		    })->toArray();
	    }

	    try {
		    $response = $this->client->call('SalesOrderFinalise', $payload);
	    } catch (Exceptions\ServiceException $e) {
		    throw $e;
	    }

	    return Order::fromXml(
		    $response->SalesOrderDetail,
		    $response->SalesOrderLines
	    );
    }

    /**
     * Create provided order.
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
     * Create or update the provided order.
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