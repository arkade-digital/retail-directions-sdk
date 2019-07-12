<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

class ItemSize extends Fluent
{
    /**
     * Retail Directions item size code.
     *
     * @var string
     */
    protected $sizeCode;

    /**
     * Retail Directions item barcode.
     *
     * @var string
     */
    protected $barcode;

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
    public function getSizeCode()
    {
        return $this->sizeCode;
    }

    /**
     * @param string $sizeCode
     * @return ItemSize
     */
    public function setSizeCode($sizeCode)
    {
        $this->sizeCode = $sizeCode;
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
     * @return ItemSize
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
        return $this;
    }

    /**
     * Create entity from provided XML element.
     *
     * @param  \SimpleXMLElement $xml
     * @return ItemSize
     */
    public static function fromXml(\SimpleXMLElement $xml) {
        $itemSize = new static;

        $itemSize->setSizeCode((string) $xml->sizeCode);
        $itemSize->setBarcode((string) $xml->sellcodeCode);

        foreach ($xml->children() as $key => $value) {
            $itemSize->{$key} = (string) $value;
        }

        return $itemSize;
    }
}