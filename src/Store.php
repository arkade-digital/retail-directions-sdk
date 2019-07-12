<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Fluent;

class Store extends Fluent
{
    /**
     * Retail Directions store code.
     *
     * @var string
     */
    protected $code;

    /**
     * Customer constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Return Retail Directions store code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Return Retail Directions store code.
     *
     * @param  string $id
     * @return Customer
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Create entity from provided XML element.
     *
     * @param  \SimpleXMLElement $xml
     * @return Store
     */
    public static function fromXml(\SimpleXMLElement $xml) {
        $store = new static;

        $store->setCode((string) $xml->storeCode);

        foreach ($xml->children() as $key => $value) {
            $store->{$key} = (string) $value;
        }

        return $store;
    }
}