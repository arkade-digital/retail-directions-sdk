<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

class StockAvailability extends Fluent
{
    /**
     * Retail Directions item colour reference.
     *
     * @var string
     */
    protected $itemColourRef;

    /**
     * Retail Directions size code.
     *
     * @var string
     */
    protected $sizeCode;

    /**
     * Retail Directions sell code.
     *
     * @var string
     */
    protected $sellCode;

    /**
     * Retail Directions store code.
     *
     * @var string
     */
    protected $storeCode;

    /**
     * Retail Directions quantity.
     *
     * @var int
     */
    protected $qty;

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
    public function getSizeCode()
    {
        return $this->sizeCode;
    }

    /**
     * @param string $sizeCode
     * @return StockAvailability
     */
    public function setSizeCode($sizeCode)
    {
        $this->sizeCode = $sizeCode;
        return $this;
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
     * @return StockAvailability
     */
    public function setSellCode($sellCode)
    {
        $this->sellCode = $sellCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getStoreCode()
    {
        return $this->storeCode;
    }

    /**
     * @param string $storeCode
     * @return StockAvailability
     */
    public function setStoreCode($storeCode)
    {
        $this->storeCode = $storeCode;
        return $this;
    }

    /**
     * @return int
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * @param int $qty
     * @return StockAvailability
     */
    public function setQty($qty)
    {
        $this->qty = $qty;
        return $this;
    }

    /**
     * Create entity from provided XML element.
     *
     * @param  \SimpleXMLElement $xml
     * @return ItemColourDetail
     */
    public static function fromXml(\SimpleXMLElement $xml) {
        $stockAvailability = new static;

        $stockAvailability->setItemColourRef((string) $xml->SKU->itemColourRef);
        $stockAvailability->setSizeCode((string) $xml->SKU->sizeCode);
        $stockAvailability->setSellCode((string) $xml->SKU->sellcodeCode);
        $stockAvailability->setStoreCode((string) $xml->Store->storeCode);
        $stockAvailability->setQty((int) $xml->quantityAvailable);

        return $stockAvailability;
    }
}