<?php

namespace Arkade\RetailDirections;

use Carbon\Carbon;
use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;

class GiftVoucherRedeemRequest extends Fluent
{
    protected $transactionAmount;

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
    public function getTransactionAmount()
    {
        return $this->transactionAmount;
    }

    /**
     * @param mixed $giftVoucherRequestCode
     * @return GiftVoucherRedeemRequest
     */
    public function setTransactionAmount($transactionAmount)
    {
        $this->transactionAmount = $transactionAmount;
        return $this;
    }


    public static function fromXml(
        \SimpleXMLElement $xml
    ) {
        $giftVoucherRedeemRequest = new static;
        $giftVoucherRedeemRequest->setTransactionAmount((string) $xml->tran_amount);

        foreach ($xml->children() as $key => $value) {
            $giftVoucherRedeemRequest->{$key} = (string) $value;
        }

        return $giftVoucherRedeemRequest;
    }
    
}