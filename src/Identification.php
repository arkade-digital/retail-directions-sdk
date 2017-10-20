<?php

namespace Arkade\RetailDirections;

use Arkade\RetailDirections\Identifications;

class Identification
{
    /**
     * Identification type.
     *
     * @var string
     */
    protected $type;

    /**
     * Identification value.
     *
     * @var string
     */
    protected $value;

    /**
     * Identification constructor.
     *
     * @param string $type
     * @param string $value
     */
    public function __construct($type, $value)
    {
        $this->type  = $type;
        $this->value = $value;
    }

    /**
     * Return identification type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return identification value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Return XML array representation.
     *
     * @return array
     */
    public function getXmlArray()
    {
        return [
            '@node' => 'CustomerIdentification',
            'identificationTypeCode' => $this->getType(),
            'customerReference' => $this->getValue()
        ];
    }

    /**
     * Create entity from provided XML element.
     *
     * @param  \SimpleXMLElement $xml
     * @return Identification
     */
    public static function fromXml(\SimpleXMLElement $xml)
    {
        if ('OMNEOIDENT' == $xml->identificationTypeCode) {
            return new Identifications\Omneo((string) $xml->customerReference);
        }

        return new static((string) $xml->identificationTypeCode, (string) $xml->customerReference);
    }
}