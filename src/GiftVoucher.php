<?php

namespace Arkade\RetailDirections;

use Carbon\Carbon;
use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;

class GiftVoucher extends Fluent
{
    protected $giftVoucherReference;

    protected $giftVoucherSchemaCode;

    protected $pin;

    protected $locationCode;

    protected $currencyCode;

    protected $maxIssueAmount;

    protected $minIssueAmount;

    protected $currentBalance;

    protected $statusInd;

    protected $locationCurrencyCode;

    protected $locationCurrentBalance;

    protected $locationMinIssueAmount;

    protected $locationMaxIssueAmount;

    protected $expiryDateTime;

    protected $amountSold;

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
    public function getLocationCode()
    {
        return $this->locationCode;
    }

    /**
     * @param mixed $locationCode
     */
    public function setLocationCode($locationCode)
    {
        $this->locationCode = $locationCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;

    }

    /**
     * @param mixed $currencyCode
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxIssueAmount()
    {
        return $this->maxIssueAmount;

    }

    /**
     * @param mixed $maxIssueAmount
     */
    public function setMaxIssueAmount($maxIssueAmount)
    {
        $this->maxIssueAmount = $maxIssueAmount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinIssueAmount()
    {
        return $this->minIssueAmount;
    }

    /**
     * @param mixed $minIssueAmount
     */
    public function setMinIssueAmount($minIssueAmount)
    {
        $this->minIssueAmount = $minIssueAmount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrentBalance()
    {
        return $this->currentBalance;
    }

    /**
     * @param mixed $cyrrentBalance
     */
    public function setCurrentBalance($currentBalance)
    {
        $this->currentBalance = $currentBalance;

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

    /**
     * @return mixed
     */
    public function getLocationCurrencyCode()
    {
        return $this->locationCurrencyCode;
    }

    /**
     * @param mixed $locationCurrencyCode
     */
    public function setLocationCurrencyCode($locationCurrencyCode)
    {
        $this->locationCurrencyCode = $locationCurrencyCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocationCurrentBalance()
    {
        return $this->locationCurrentBalance;
    }

    /**
     * @param mixed $locationCurrentBalance
     */
    public function setLocationCurrentBalance($locationCurrentBalance)
    {
        $this->locationCurrentBalance = $locationCurrentBalance;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocationMinIssueAmount()
    {
        return $this->locationMinIssueAmount;
    }

    /**
     * @param mixed $locationMinIssueAmount
     */
    public function setLocationMinIssueAmount($locationMinIssueAmount)
    {
        $this->locationMinIssueAmount = $locationMinIssueAmount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocationMaxIssueAmount()
    {
        return $this->locationMaxIssueAmount;
    }

    /**
     * @param mixed $locationMaxIssueAmount
     */
    public function setLocationMaxIssueAmount($locationMaxIssueAmount)
    {
        $this->locationMaxIssueAmount = $locationMaxIssueAmount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpiryDateTime()
    {
        return $this->expiryDateTime;
    }

    /**
     * @param mixed $expiryDateTime
     */
    public function setExpiryDateTime($expiryDateTime)
    {
        $this->expiryDateTime = $expiryDateTime;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAmountSold()
    {
        return $this->amountSold;
    }

    /**
     * @param mixed $amountSold
     */
    public function setAmountSold($amountSold)
    {
        $this->amountSold = $amountSold;

        return $this;
    }

    public static function fromXml(
        \SimpleXMLElement $xml
    ) {
        $giftVoucher = new static;

        $giftVoucher->setGiftVoucherReference((string) $xml->giftvoucher_reference);
        $giftVoucher->setGiftVoucherSchemaCode((string) $xml->giftvoucherscheme_code);
        $giftVoucher->setLocationCode((string) $xml->location_code);
        $giftVoucher->setStatusInd((string) $xml->status_ind);
        $giftVoucher->setCurrencyCode((string) $xml->currency_code);
        $giftVoucher->setCurrentBalance((string) $xml->current_balance);
        $giftVoucher->setMaxIssueAmount((string) $xml->max_issue_amount);
        $giftVoucher->setMinIssueAmount((string) $xml->min_issue_amount);
        $giftVoucher->setLocationCurrencyCode((string) $xml->loc_currency_code);
        $giftVoucher->setLocationCurrentBalance((string) $xml->current_balance_loc);
        $giftVoucher->setLocationMaxIssueAmount((string) $xml->max_issue_amount_loc);
        $giftVoucher->setLocationMinIssueAmount((string) $xml->min_issue_amount_loc);
        $giftVoucher->setExpiryDateTime(Carbon::parse((string) $xml->expiry_datetime));
        $giftVoucher->setPin((string) $xml->pin);
        $giftVoucher->setAmountSold((string) $xml->amount_sold);

        $giftVoucher->__set('last_reference_type_ind', (string) $xml->last_reference_type_ind);
        $giftVoucher->__set('last_doc_line_id', (string) $xml->last_doc_line_id);
        $giftVoucher->__set('last_transaction_datetime', (string) $xml->last_transaction_datetime);
        $giftVoucher->__set('issue_store', (string) $xml->issue_store);
        $giftVoucher->__set('issue_store_description', (string) $xml->issue_store_description);
        $giftVoucher->__set('issue_datetime', Carbon::parse((string) $xml->issue_datetime));


        return $giftVoucher;
    }
    
}