<?php

namespace Arkade\RetailDirections;

class LoyaltyCustomer extends Customer
{
    /**
     * Parent customer.
     * @var Customer
     */
    protected $parent;

    /**
     * Set the parent customer object.
     *
     * @param Customer $customer
     * @return $this
     */
    public function setParentCustomer(Customer $customer)
    {
        $this->parent = $customer;

        $this->attributes['id']         = $customer->getId();
        $this->attributes['customerId'] = $customer->getId();
        $this->attributes['customerID'] = $customer->getId();
        $this->attributes['CustomerID'] = $customer->getId();

        $this->setId($customer->getId());

        return $this;
    }

    /**
     * Create entity from provided XML element.
     *
     * @param  \SimpleXMLElement $xml
     * @param  \SimpleXMLElement $identificationsXml
     * @param  \SimpleXMLElement $addressesXml
     * @return LoyaltyCustomer
     */
    public static function fromXml(
        \SimpleXMLElement $xml,
        \SimpleXMLElement $identificationsXml = null,
        \SimpleXMLElement $addressesXml = null
    ) {
        $customer = new static;

        $customer->setId((string) $xml->customerId);

        // Handle mapping inconsistent fields.
        $customer->loyaltygroupId = (string) $xml->loyaltyGroupId;

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

        return $customer;
    }
}