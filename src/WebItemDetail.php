<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Fluent;

class WebItemDetail extends Fluent
{
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
     * @return string
     */
    public function getItemCode()
    {
        return $this->itemCode;
    }

    /**
     * @param string $itemCode
     * @return WebItemDetail
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
     * @return WebItemDetail
     */
    public static function fromXml(\SimpleXMLElement $xml) {
        $webItemDetail = new static;

        $webItemDetail->setItemCode((string) $xml->itemCode);

        foreach ($xml->children() as $key => $value) {
            $webItemDetail->{$key} = (string) $value;
        }

        return $webItemDetail;
    }
}