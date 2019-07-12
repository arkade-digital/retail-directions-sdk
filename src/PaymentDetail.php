<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;

class PaymentDetail extends Fluent
{
    /**
     * Retail Directions payment type
     *
     * @var string
     */
    protected $paymentType;

    /**
     * Retail Directions payment reference number
     *
     * @var string
     */
    protected $paymentReferenceNumber;

    /**
     * Retail Directions payment amount.
     *
     * @var string
     */
    protected $paymentAmount;

    /**
     * Retail Directions currency code.
     *
     * @var string
     */
    protected $currencyCode;

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
	 * @return string
	 */
	public function getPaymentType() {
		return $this->paymentType;
	}

	/**
	 * @param string $paymentType
	 *
	 * @return PaymentDetail
	 */
	public function setPaymentType($paymentType) : PaymentDetail {
		$this->paymentType = $paymentType;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPaymentReferenceNumber() {
		return $this->paymentReferenceNumber;
	}

	/**
	 * @param string $paymentReferenceNumber
	 *
	 * @return PaymentDetail
	 */
	public function setPaymentReferenceNumber($paymentReferenceNumber) : PaymentDetail {
		$this->paymentReferenceNumber = $paymentReferenceNumber;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPaymentAmount() {
		return $this->paymentAmount;
	}

	/**
	 * @param string $paymentAmount
	 *
	 * @return PaymentDetail
	 */
	public function setPaymentAmount($paymentAmount) : PaymentDetail {
		$this->paymentAmount = $paymentAmount;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCurrencyCode() {
		return $this->currencyCode;
	}

	/**
	 * @param string $currencyCode
	 *
	 * @return PaymentDetail
	 */
	public function setCurrencyCode($currencyCode) : PaymentDetail {
		$this->currencyCode = $currencyCode;

		return $this;
	}

	/**
	 * Return XML array representation.
	 *
	 * @return array
	 */
	public function getXmlArray()
	{
        return [
	        '@node' => 'PaymentDetail',
	        'paymentType' => $this->getPaymentType(),
	        'paymentReferenceNumber' => $this->getPaymentReferenceNumber(),
	        'paymentAmount' => $this->getPaymentAmount(),
	        'currencyCode' => $this->getCurrencyCode(),
        ];
    }
}