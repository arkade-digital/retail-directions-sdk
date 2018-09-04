<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

class ItemColourSize extends Fluent
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
     * @return WebItemColourSize
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
     * @return WebItemColourSize
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
     * @return WebItemColourSize
     */
    public static function fromXml(\SimpleXMLElement $xml) {
        $webItemColourSize = new static;

        $webItemColourSize->setSizeCode((string) $xml->sizeCode);
        $webItemColourSize->setBarcode((string) $xml->sellcodeCode);

        foreach ($xml->children() as $key => $value) {
            $webItemColourSize->{$key} = (string) $value;
        }

        return $webItemColourSize;
    }
}