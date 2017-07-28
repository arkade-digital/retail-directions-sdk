<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;

class Customer extends Fluent
{
    /**
     * Retail Directions customer ID.
     *
     * @var string
     */
    protected $id;

    /**
     * Collection of addresses attached to customer.
     *
     * @var Collection
     */
    protected $addresses;

    /**
     * Customer constructor.
     *
     * @param string $id
     * @param array  $attributes
     */
    public function __construct($id, $attributes = [])
    {
        parent::__construct($attributes);

        $this->id = $id;
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
     * Return collection of addresses attached to customer.
     *
     * @return Collection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Create entity from provided XML element.
     *
     * @param  \SimpleXMLElement $xml
     * @return Customer
     */
    public static function fromXml(\SimpleXMLElement $xml)
    {
        $customer = new static((string) $xml->customerId);

        foreach ($xml->children() as $key => $value) {
            $customer->{$key} = (string) $value;
        }

        return $customer;
    }
}