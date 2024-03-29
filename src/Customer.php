<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class Customer extends Fluent
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
     * Collection of loyalty attached to customer.
     *
     * @var Collection[CustomerLoyalty]
     */
    protected $customerLoyalty;

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
        $this->customerLoyalty = new Collection;
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
     * Return collection of loyalty attached to customer.
     *
     * @return Collection
     */
    public function getCustomerLoyalty()
    {
        return $this->customerLoyalty;
    }

    /**
     * Push provided loyalty on to collection.
     *
     * @param  CustomerLoyalty $customerLoyalty
     * @return Customer
     */
    public function pushCustomerLoyalty(CustomerLoyalty $customerLoyalty)
    {
        $this->customerLoyalty->push($customerLoyalty);

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
     * Create entity from provided XML element.
     *
     * @param  \SimpleXMLElement $xml
     * @param  \SimpleXMLElement $identificationsXml
     * @param  \SimpleXMLElement $addressesXml
     * @return Customer
     */
    public static function fromXml(
        \SimpleXMLElement $xml,
        \SimpleXMLElement $identificationsXml = null,
        \SimpleXMLElement $addressesXml = null,
        \SimpleXMLElement $loyaltyXML = null
    ) {
        $customer = new static;

        $customer->setId((string) $xml->customerId);

        foreach ($xml->children() as $key => $value) {
            $customer->{$key} = (string) $value;
        }

        if ($identificationsXml) {
            foreach ($identificationsXml->CustomerIdentification as $identification) {
                $customer->pushIdentification(Identification::fromXml($identification));
            }
        }

        if ($addressesXml) {
            foreach ($addressesXml->Address as $address) {
                $customer->getAddresses()->push(Address::fromXml($address));
            }
        }


        if($loyaltyXML) {
            foreach($loyaltyXML as $loyaltyCustomers) {
                foreach($loyaltyCustomers as $loyaltyCustomer) {
                    $customer->pushCustomerLoyalty(CustomerLoyalty::fromXml($loyaltyCustomer));
                }
            }
        }

        return $customer;
    }
}