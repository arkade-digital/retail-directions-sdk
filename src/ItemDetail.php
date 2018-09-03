<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Fluent;

class ItemDetail extends Fluent
{
    /**
     * Retail Directions item sell code.
     *
     * @var string
     */
    protected $sellCode;

    /**
     * Retail Directions item code.
     *
     * @var string
     */
    protected $itemCode;

    /**
     * Retail Directions barcode.
     *
     * @var string
     */
    protected $barcode;

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
     * @return string
     */
    public function getSellCode()
    {
        return $this->sellCode;
    }

    /**
     * @param string $sellCode
     * @return ItemDetail
     */
    public function setSellCode($sellCode)
    {
        $this->sellCode = $sellCode;
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
     * @return ItemDetail
     */
    public function setItemCode($itemCode)
    {
        $this->itemCode = $itemCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param string $barcode
     * @return ItemDetail
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
        return $this;
    }

    /**
     * @return string
     */
    public function getItemColourRef()
    {
        return $this->itemColourRef;
    }

    /**
     * @param string $itemColourRef
     * @return ItemDetail
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
     * @return ItemDetail
     */
    public static function fromXml(\SimpleXMLElement $xml) {
        $itemDetail = new static;

        $itemDetail->setSellCode((string) $xml->sellcodeCode);
        $itemDetail->setItemCode((string) $xml->itemCode);
        $itemDetail->setBarcode((string) $xml->barcodeCode);
        $itemDetail->setItemColourRef((string) $xml->itemColourRef);

        foreach ($xml->children() as $key => $value) {
            $itemDetail->{$key} = (string) $value;
        }

        return $itemDetail;
    }
}