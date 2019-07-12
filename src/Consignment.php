<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;

class Consignment extends Fluent
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $transportcoCode;

    /**
     * @var string
     */
    protected $transportcoDesc;

    /**
     * @var array[]|Collection
     */
    protected $lineItems;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->lineItems = new Collection();
    }

	/**
	 * Return Retail Directions customer ID.
	 *
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Return Retail Directions customer ID.
	 *
	 * @param  string $id
	 *
	 * @return Customer
	 */
	public function setId($id) {
		$this->id = $id;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTransportcoCode() {
		return $this->transportcoCode;
	}

	/**
	 * @param string $transportcoCode
	 *
	 * @return Consignment
	 */
	public function setTransportcoCode($transportcoCode) : Consignment {
		$this->transportcoCode = $transportcoCode;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTransportcoDesc() {
		return $this->transportcoDesc;
	}

	/**
	 * @param string $transportcoDesc
	 *
	 * @return Consignment
	 */
	public function setTransportcoDesc($transportcoDesc) : Consignment {
		$this->transportcoDesc = $transportcoDesc;

		return $this;
	}

	/**
	 * @return array[]|Collection
	 */
	public function getLineItems() {
		return $this->lineItems;
	}

	/**
	 * Create entity from provided XML element.
	 *
	 * @param  \SimpleXMLElement $xml
	 * @return static
	 */
	public static function fromXml(
		\SimpleXMLElement $xml
	) {
		$entity = new static;
		$entity->setId((string) $xml->consignmentNumber);
		$entity->setTransportcoCode($xml->transportcoCode);
		$entity->setTransportcoDesc($xml->transportcoDesc);

		foreach ($xml->children() as $key => $value) {
			$entity->{$key} = (string) $value;
		}

		foreach ($xml->ConsignmentLines->ConsignmentLine as $consignment) {
			$entity->getLineItems()->push([
				'sku' => (string)data_get($consignment, 'sellcodeCode'),
				'quantity' => (string)data_get($consignment, 'quantity'),
			]);
		}

		return $entity;
	}
}