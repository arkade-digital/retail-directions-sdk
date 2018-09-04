<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Fluent;

class ItemColourDetail extends Fluent
{
    /**
     * Retail Directions item colour reference.
     *
     * @var string
     */
    protected $itemColourRef;

    /**
     * Retail Directions item code.
     *
     * @var string
     */
    protected $itemCode;

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
     * @return ItemColourDetail
     */
    public function setItemColourRef($itemColourRef)
    {
        $this->itemColourRef = $itemColourRef;

        return $this;
    }

    /**
     * @return string
     */
    public function getItemCode()
    {
        return $this->itemCode;
    }

    /**
     * @param string $itemCode
     * @return ItemColourDetail
     */
    public function setItemCode($itemCode)
    {
        $this->itemCode = $itemCode;
        return $this;
    }

    /**
     * Create entity from provided XML element.
     *
     * @param  \SimpleXMLElement $xml
     * @return ItemColourDetail
     */
    public static function fromXml(\SimpleXMLElement $xml) {
        $itemColourDetail = new static;

        $itemColourDetail->setItemColourRef((string) $xml->itemColourRef);
        $itemColourDetail->setItemCode((string) $xml->itemCode);

        foreach ($xml->children() as $key => $value) {
            $itemColourDetail->{$key} = (string) $value;
        }

        return $itemColourDetail;
    }
}