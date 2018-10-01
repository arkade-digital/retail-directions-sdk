<?php

namespace Arkade\RetailDirections\Modules;

use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Collection;
use Arkade\RetailDirections\Address;
use Arkade\RetailDirections\Customer;
use Arkade\RetailDirections\Exceptions;
use Arkade\RetailDirections\Identification;

class Customers extends AbstractModule {
	/**
	 * Return a single customer by ID.
	 *
	 * @param  string $id
	 * @param  Carbon|null $datetime
	 *
	 * @return Customer
	 * @throws Exceptions\NotFoundException
	 * @throws Exceptions\ServiceException
	 */
	public function findById($id, Carbon $datetime = null) {
		try {
			$response = $this->client->call('CustomerGet', [
				'CustomerGet' => [
					'customerId'      => $id,
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

		return Customer::fromXml(
			$response->Customer,
			$response->CustomerIdentifications,
			$response->Addresses
		);
	}

	/**
	 * Return a collection of customers for an email address.
	 *
	 * @param  string $email
	 * @param  Carbon|null $datetime
	 *
	 * @return Collection
	 * @throws Exceptions\NotFoundException
	 * @throws Exceptions\ServiceException
	 */
	public function findByEmail($email, Carbon $datetime = null) {
		try {
			$response = $this->client->call('CustomerGetByEmail', [
				'CustomerGetByEmail' => [
					'emailAddress'    => $email,
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

		if ($collection->isEmpty()) {
			throw (new Exceptions\NotFoundException)
				->setHistoryContainer($this->client->getHistoryContainer());
		}

		return $collection;
	}

	/**
	 * Return collection of customers for a provided identification.
	 *
	 * @param  Identification $identification
	 * @param  Carbon|null $datetime
	 *
	 * @return Collection
	 * @throws Exceptions\NotFoundException
	 * @throws Exceptions\ServiceException
	 */
	public function findByIdentification(Identification $identification, Carbon $datetime = null) {
		$response = $this->client->call('CustIdentByCustomerRefFind', [
			'CustIdentByCustomerRefFind' => [
				'customerReference'      => $identification->getValue(),
				'identificationTypeCode' => $identification->getType(),
			]
		]);

		$collection = new Collection;

		foreach ($response->CustomerIdentifications->CustomerIdentification as $customerIdentification) {
			$collection->push($this->findById($customerIdentification->customerId, $datetime));
		}

		if ($collection->isEmpty()) {
			throw (new Exceptions\NotFoundException)
				->setHistoryContainer($this->client->getHistoryContainer());
		}

		return $collection;
	}

	/**
	 * Return collection of customers for a provided identification.
	 *
	 * @param string $customerId
	 * @param Carbon|null $datetime
	 *
	 * @return Collection
	 * @throws Exceptions\NotFoundException
	 * @throws Exceptions\ServiceException
	 */
	public function findByCustomerSite($customerId) {
		$response = $this->client->call('CustomerSiteFind', [
			'CustomerSiteFind' => [
				'customerId' => $customerId,
			]
		]);

		return \Arkade\RetailDirections\CustomerSite::fromXml(
			$response->CustomerSites
		);
	}

	/**
	 * Return collection of customers for a provided identification.
	 *
	 * @param string $customerId
	 * @param Carbon|null $datetime
	 *
	 * @return Collection
	 * @throws Exceptions\NotFoundException
	 * @throws Exceptions\ServiceException
	 */
	public function createCustomerSite(Customer $customer) {
		$response = $this->client->call('CustomerSiteEdit', [
			'CustomerSite' => [
				'locationRef' => 'WEB',
				'customerId'  => $customer->getId(),
				'activeInd'   => 'Y',
				'defaultInd'  => 'Y',
				'Address1'    => $customer->get('address1'),
				'suburb'      => $customer->get('suburb'),
				'state'       => $customer->get('state'),
				'countryCode' => $customer->get('countryCode') ?: 'AU',
				'postCode'    => $customer->get('postCode'),
			]
		]);

		return \Arkade\RetailDirections\CustomerSite::fromXml(
			$response->CustomerSite
		);
	}

	/**
	 * Return collection of customers for a provided identification.
	 *
	 * @param  Identification $identification
	 * @param  Carbon|null $datetime
	 *
	 * @return Collection
	 * @throws Exceptions\NotFoundException
	 * @throws Exceptions\ServiceException
	 */
	public function findChangedCustomers(Carbon $from, Carbon $to) {
		$response = $this->client->call('ChangedCustomerFind', [
			'ChangedCustomerFind' => [
				'FromDateTime' => $this->client->formatDateTime($from),
				'ToDateTime'   => $this->client->formatDateTime($to),
			]
		]);

		$collection = new Collection;

		foreach ($response->ChangedCustomers->Customer as $customerIdentification) {
			$collection->push($this->findById($customerIdentification->customerId));
		}

		if ($collection->isEmpty()) {
			throw (new Exceptions\NotFoundException)
				->setHistoryContainer($this->client->getHistoryContainer());
		}

		return $collection;
	}

	/**
	 * Create provided customer.
	 *
	 * @param  Customer $customer
	 * @param  Carbon $datetime Optional datetime for findById request
	 *
	 * @return Customer
	 * @throws Exceptions\AlreadyExistsException
	 * @throws Exceptions\ValidationException
	 * @throws Exceptions\ServiceException
	 */
	public function create(Customer $customer, Carbon $datetime = null) {
		if ($customer->getId()) {
			try {
				if ($this->findById($customer->getId(), $datetime)) {
					throw new Exceptions\AlreadyExistsException;
				}
			} catch (Exceptions\NotFoundException $e) {
			}
		}

		return $this->persist($customer);
	}

	/**
	 * Update provided customer.
	 *
	 * @param  Customer $customer
	 * @param  Carbon $datetime Optional datetime for findById request
	 *
	 * @return Customer
	 * @throws Exceptions\NotFoundException
	 * @throws Exceptions\AlreadyExistsException
	 * @throws Exceptions\ValidationException
	 * @throws Exceptions\ServiceException
	 */
	public function update(Customer $customer, Carbon $datetime = null) {
		if (!$customer->getId()) {
			throw new DomainException('You must provide an ID when updating a customer. Try using findByIdentification() or findByEmail() first.');
		}

		$this->findById($customer->getId(), $datetime);

		return $this->persist($customer);
	}

	/**
	 * Create or update the provided customer entity.
	 *
	 * @param  Customer $customer
	 *
	 * @return Customer
	 * @throws Exceptions\AlreadyExistsException
	 * @throws Exceptions\ValidationException
	 * @throws Exceptions\ServiceException
	 */
	protected function persist(Customer $customer) {
		$payload = ['Customer' => $customer->getAttributes()];

		if ($customer->getId()) {
			$payload['Customer']['customerId'] = $customer->getId();
		}

		try {
			$response = $this->client->call('CustomerEdit', $payload);
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

		return Customer::fromXml(
			$response->Customer,
			$response->CustomerIdentifications,
			$response->Addresses
		);
	}
}