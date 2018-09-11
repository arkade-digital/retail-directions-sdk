<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Collection;
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
     * Retail Directions item sizes.
     *
     * @var Collection
     */
    protected $itemSizes;

    /**
     * Retail Directions item editorials.
     *
     * @var Collection
     */
    protected $itemEditorials;

    /**
     * Retail Direction  original xml 
     *
     * @var 
     */
    protected $xml;

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
     * @return Collection
     */
    public function getItemSizes()
    {
        return $this->itemSizes ?: $this->itemSizes = new Collection;
    }

    /**
     * @param Collection $itemSizes
     * @return ItemColourDetail
     */
    public function setItemSizes($itemSizes)
    {
        $this->itemSizes = $itemSizes;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getItemEditorials()
    {
        return $this->itemEditorials ?: $this->itemEditorials = new Collection;
    }

    /**
     * @param Collection $itemEditorials
     * @return ItemColourDetail
     */
    public function setItemEditorials($itemEditorials)
    {
        $this->itemEditorials = $itemEditorials;
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

        $itemColourDetail->setItemColourRef((string) $xml->itemcolourRef);
        $itemColourDetail->setItemCode((string) $xml->itemCode);

        foreach ($xml->children() as $key => $value) {
            if($key === 'attributes') continue;
            if($key === 'editorials') continue;
            if($key === 'quantitiesAvailable') continue;
            $itemColourDetail->{$key} = (string) $value;
        }

        if ($xml->editorials) {
            foreach ($xml->editorials->edtorial as $itemEditorial) {
                $itemColourDetail->getItemEditorials()->push(ItemEditorial::fromXml($itemEditorial));
            }
        }

        if ($xml->quantitiesAvailable) {
            foreach ($xml->quantitiesAvailable->size as $itemSize) {
                $itemColourDetail->getItemSizes()->push(ItemSize::fromXml($itemSize));
            }
        }

        return $itemColourDetail;
    }
}