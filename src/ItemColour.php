<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Fluent;

class ItemColour extends Fluent
{
    /**
     * Retail Directions item colour reference.
     *
     * @var string
     */
    protected $itemColourRef;

    /**
     * constructor.
     *
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Return Retail Directions item colour reference.
     *
     * @return string
     */
    public function getItemColourRef()
    {
        return $this->itemColourRef;
    }

    /**
     * Return Retail Directions item colour reference.
     *
     * @param  string $id
     * @return Customer
     */
    public function setItemColourRef($itemColourRef)
    {
        $this->itemColourRef = $itemColourRef;

        return $this;
    }

    /**
     * Create entity from provided XML element.
     *
     * @param  \SimpleXMLElement $xml
     * @return ItemColour
     */
    public static function fromXml(\SimpleXMLElement $xml) {
        $itemColour = new static;

        $itemColour->setItemColourRef((string) $xml->itemColourRef);

        return $itemColour;
    }
}