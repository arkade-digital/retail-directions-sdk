<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Fluent;

class Address extends Fluent
{
    /**
     * Retail Directions address ID.
     *
     * @var string
     */
    protected $id;

    /**
     * Address constructor.
     *
     * @param string $id
     * @param array  $attributes
     */
    public function __construct($id, $attributes = [])
    {
        parent::__construct($attributes);

        $this->id = $id;
    }

    /**
     * Return Retail Directions address ID.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Create entity from provided XML element.
     *
     * @param  \SimpleXMLElement $xml
     * @return Address
     */
    public static function fromXml(\SimpleXMLElement $xml)
    {
        $address = new static((string) $xml->addressId);

        foreach ($xml->children() as $key => $value) {
            $address->{$key} = (string) $value;
        }

        return $address;
    }
}