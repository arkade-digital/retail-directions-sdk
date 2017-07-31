<?php

namespace Arkade\RetailDirections;

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
            'customerIdentificationId' => $this->getType(),
            'customerReference' => $this->getValue()
        ];
    }
}