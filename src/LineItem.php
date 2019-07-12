<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

class LineItem extends Fluent {
	/**
	 * Retail Directions customer ID.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Customer constructor.
	 *
	 * @param array $attributes
	 */
	public function __construct(array $attributes = []) {
		parent::__construct($attributes);
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
	 * Return XML array representation.
	 *
	 * @return array
	 */
	public function getXmlArray() {
		$xmlArray = [
			'@node'                  => 'SalesOrderLine',
			'locationRef'            => $this->get('locationRef'),
			'sellcodeCode'           => $this->get('sellcodeCode'),
			'orderQuantity'          => $this->get('orderQuantity'),
			'unitPrice'              => $this->get('unitPrice'),
			'listUnitPrice'          => $this->get('listUnitPrice'),
			'effectivePriceOverflow' => $this->get('effectivePriceOverflow'),
		];

		if ($this->get('discounts')) {
			$xmlArray['SalesOrderLineBenefits'] = collect($this->get('discounts'))->map(function ($item) {
				return [
					'@node'               => 'SalesOrderLineBenefit',
					'salesOrderBenefitID' => array_get($item, 'salesOrderBenefitID'),
					'benefitAmount'       => (float)array_get($item, 'benefitAmount'),
					'benefitAppliedCount' => 1,
					'benefitItem'         => 'Y',
					'eligibleItem'        => 'Y',
				];
			})->toArray();
		}

		return $xmlArray;
	}

	/**
	 * Create entity from provided XML element.
	 *
	 * @param  \SimpleXMLElement $xml
	 * @param  \SimpleXMLElement $lineItemsXml
	 * @return static
	 */
	public static function fromXml(
		\SimpleXMLElement $xml
	) {
		$entity = new static;
		$entity->setId((string) $xml->sellcodeCode);

		foreach ($xml->children() as $key => $value) {
			$entity->{$key} = (string) $value;
		}

		return $entity;
	}
}