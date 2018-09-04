<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

class WebItemSize extends Fluent
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
     * @return WebItemSize
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
     * @return WebItemSize
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
     * @return WebItemSize
     */
    public static function fromXml(\SimpleXMLElement $xml) {
        $webItemSize = new static;

        $webItemSize->setSizeCode((string) $xml->sizeCode);
        $webItemSize->setBarcode((string) $xml->sellcodeCode);

        foreach ($xml->children() as $key => $value) {
            $webItemSize->{$key} = (string) $value;
        }

        return $webItemSize;
    }
}