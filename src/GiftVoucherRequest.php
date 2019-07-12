<?php

namespace Arkade\RetailDirections;

use Carbon\Carbon;
use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;

class GiftVoucherRequest extends Fluent
{
    protected $giftVoucherRequestCode;

    protected $giftVoucherReference;

    protected $giftVoucherSchemaCode;

    protected $pin;

    protected $statusInd;

    /**
     * Customer constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

    }

    /**
     * @return mixed
     */
    public function getGiftVoucherRequestCode()
    {
        return $this->giftVoucherRequestCode;
    }

    /**
     * @param mixed $giftVoucherRequestCode
     * @return GiftVoucherRequest
     */
    public function setGiftVoucherRequestCode($giftVoucherRequestCode)
    {
        $this->giftVoucherRequestCode = $giftVoucherRequestCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGiftVoucherReference()
    {
        return $this->giftVoucherReference;
    }

    /**
     * @param mixed $giftVoucherReference
     */
    public function setGiftVoucherReference($giftVoucherReference)
    {
        $this->giftVoucherReference = $giftVoucherReference;
        
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGiftVoucherSchemaCode()
    {
        return $this->giftVoucherSchemaCode;
    }

    /**
     * @param mixed $giftVoucherSchemaCode
     */
    public function setGiftVoucherSchemaCode($giftVoucherSchemaCode)
    {
        $this->giftVoucherSchemaCode = $giftVoucherSchemaCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * @param mixed $pin
     */
    public function setPin($pin)
    {
        $this->pin = $pin;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatusInd()
    {
        return $this->statusInd;
    }

    /**
     * @param mixed $statusInd
     */
    public function setStatusInd($statusInd)
    {
        $this->statusInd = $statusInd;

        return $this;
    }

    public static function fromXml(
        \SimpleXMLElement $xml
    ) {
        $giftVoucherRequest = new static;

        $giftVoucherRequest->setGiftVoucherRequestCode((string) $xml->giftvoucherrequest_code);
        $giftVoucherRequest->setGiftVoucherReference((string) $xml->giftvoucher_reference);
        $giftVoucherRequest->setGiftVoucherSchemaCode((string) $xml->giftvoucherscheme_code);
        $giftVoucherRequest->setStatusInd((string) $xml->status_ind);
        $giftVoucherRequest->setPin((string) $xml->pin);

        foreach ($xml->children() as $key => $value) {
            $giftVoucherRequest->{$key} = (string) $value;
        }

        return $giftVoucherRequest;
    }
    
}