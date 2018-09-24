<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;

class LineItem extends Fluent
{
    /**
     * Retail Directions customer ID.
     *
     * @var string
     */
    protected $id;

    /**
     * Collection of identifications attached to customer.
     *
     * @var Collection[Identification]
     */
    protected $identifications;

    /**
     * Collection of addresses attached to customer.
     *
     * @var Collection[Address]
     */
    protected $addresses;

    /**
     * Customer constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->identifications = new Collection;
        $this->addresses = new Collection;
    }

    /**
     * Return Retail Directions customer ID.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return Retail Directions customer ID.
     *
     * @param  string $id
     * @return Customer
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Return collection of identifications attached to customer.
     *
     * @return Collection
     */
    public function getIdentifications()
    {
        return $this->identifications;
    }

    /**
     * Push provided identification on to collection.
     *
     * @param  Identification $identification
     * @return Customer
     */
    public function pushIdentification(Identification $identification)
    {
        $this->identifications->push($identification);

        return $this;
    }

    /**
     * Return collection of addresses attached to customer.
     *
     * @return Collection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Return whether or not this customer exists (has an ID).
     *
     * @return bool
     */
    public function exists()
    {
        return !! $this->id;
    }

	/**
	 * Return XML array representation.
	 *
	 * @return array
	 */
	public function getXmlArray()
	{
        return [
	        '@node' => 'SalesOrderLine',
	        'locationRef' => $this->get('locationRef'),
	        'sellcodeCode' => $this->get('sellcodeCode'),
	        'orderQuantity' => $this->get('orderQuantity'),
	        'unitPrice' => $this->get('unitPrice'),
        ];
    }
}