<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

class WebItemColour extends Fluent
{
    /**
     * Retail Directions item colour reference.
     *
     * @var string
     */
    protected $itemColourRef;

    /**
     * Retail Directions item colour code.
     *
     * @var string
     */
    protected $colourCode;

    /**
     * Retail Directions Web Item Colour Sizes.
     *
     * @var Collection
     */
    protected $webItemColourSizes;

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
     * @return string
     */
    public function getColourCode()
    {
        return $this->colourCode;
    }

    /**
     * @param string $colourCode
     * @return WebItemColour
     */
    public function setColourCode($colourCode)
    {
        $this->colourCode = $colourCode;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getWebItemColourSizes()
    {
        return $this->webItemColourSizes ?: $this->webItemColourSizes = new Collection;
    }

    /**
     * @param Collection $webItemColourSizes
     * @return WebItemColour
     */
    public function setWebItemColourSizes($webItemColourSizes)
    {
        $this->webItemColourSizes = $webItemColourSizes;
        return $this;
    }

    /**
     * Create entity from provided XML element.
     *
     * @param  \SimpleXMLElement $xml
     * @return WebItemColour
     */
    public static function fromXml(\SimpleXMLElement $xml) {
        $webItemColour = new static;

        $webItemColour->setItemColourRef((string) $xml->itemColourReference);
        $webItemColour->setColourCode((string) $xml->colourCode);

        foreach ($xml->children() as $key => $value) {
            $webItemColour->{$key} = (string) $value;
        }

        if ($xml->WebItemColourSizeList) {
            foreach ($xml->WebItemColourSizeList->WebItemColourSize as $webItemColourSize) {
                $webItemColour->getWebItemColourSizes()->push(WebItemColourSize::fromXml($webItemColourSize));
            }
        }

        return $webItemColour;
    }
}