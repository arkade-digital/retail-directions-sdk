<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;

class CustomerSite extends Fluent
{
    /**
     * Retail Directions customer ID.
     *
     * @var string
     */
    protected $id;

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
     * Create entity from provided XML element.
     *
     * @param  \SimpleXMLElement $xml
     * @param  \SimpleXMLElement $identificationsXml
     * @param  \SimpleXMLElement $addressesXml
     * @return CustomerSite
     */
    public static function fromXml(
        \SimpleXMLElement $xml
    ) {
        $site = new static;
        $site->setId((string) $xml->locationRef);

        foreach ($xml->children() as $key => $value) {
            $site->{$key} = (string) $value;
        }

        return $site;
    }
}