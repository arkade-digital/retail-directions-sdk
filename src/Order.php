<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;

class Order extends Fluent
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
     * @var Collection[Identification]|Identification[]
     */
    protected $identifications;

    /**
     * Collection of addresses attached to customer.
     *
     * @var Collection[Address]|Address[]
     */
    protected $addresses;

    /**
     * Collection of addresses attached to customer.
     *
     * @var Collection[LineItem]|LineItem[]
     */
    protected $lineItems;

    /**
     * Order constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->identifications = new Collection;
        $this->addresses = new Collection;
        $this->lineItems = new Collection;
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
	 * @return Collection|LineItem[]
	 */
	public function getLineItems() {
		return $this->lineItems;
	}

	/**
	 * @return Collection
	 */
	public function getTotal() {
		return $this->getLineItems()->sum('');
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
     * Create entity from provided XML element.
     *
     * @param  \SimpleXMLElement $xml
     * @param  \SimpleXMLElement $lineItemsXml
     * @return Order
     */
    public static function fromXml(
        \SimpleXMLElement $xml,
        \SimpleXMLElement $lineItemsXml = null
    ) {
	    $order = new static;

	    $order->setId((string) $xml->salesorderCode);

	    foreach ($xml->children() as $key => $value) {
		    $order->{$key} = (string) $value;
	    }

        return $order;
    }
}