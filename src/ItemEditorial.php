<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

class ItemEditorial extends Fluent
{
    /**
     * Retail Directions editorial type code.
     *
     * @var string
     */
    protected $typeCode;

    /**
     * Retail Directions editorial text.
     *
     * @var string
     */
    protected $text;

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
    public function getTypeCode()
    {
        return $this->typeCode;
    }

    /**
     * @param string $typeCode
     * @return ItemEditorial
     */
    public function setTypeCode($typeCode)
    {
        $this->typeCode = $typeCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return ItemEditorial
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Create entity from provided XML element.
     *
     * @param  \SimpleXMLElement $xml
     * @return WebItemColourSize
     */
    public static function fromXml(\SimpleXMLElement $xml) {
        $itemEditorial = new static;

        $itemEditorial->setTypeCode((string) $xml->noteTypeCode);
        $itemEditorial->setText((string) $xml->noteText);

        return $itemEditorial;
    }
}