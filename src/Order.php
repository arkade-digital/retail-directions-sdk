<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;

class Order extends Fluent {
	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var LineItem[]|Collection]
	 */
	protected $lineItems;

	/**
	 * @var Consignment[]|Collection]
	 */
	protected $consignments;

	/**
	 * Order constructor.
	 *
	 * @param array $attributes
	 */
	public function __construct(array $attributes = []) {
		parent::__construct($attributes);

		$this->lineItems   = new Collection;
		$this->consignments = new Collection;
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
     * @param Collection|LineItem[] $lineItems
     *
     * @return Collection|LineItem[]
     */
    public function setLineItems($lineItems) {
        $this->lineItems = $lineItems;

        return $this->lineItems;
    }

	/**
	 * @return Collection|LineItem[]
	 */
	public function getLineItems() {
		return $this->lineItems;
	}

	/**
	 * @return Collection
	 */
	public function getConsignments() {
		return $this->consignments;
	}

	/**
	 * Return whether or not this customer exists (has an ID).
	 *
	 * @return bool
	 */
	public function exists() {
		return !!$this->id;
	}

	/**
	 * Create entity from provided XML element.
	 *
	 * @param  \SimpleXMLElement $xml
	 * @param  \SimpleXMLElement $lineItemsXml
	 * @param  \SimpleXMLElement $consignment
	 *
	 * @return Order
	 */
	public static function fromXml(
		\SimpleXMLElement $xml,
		\SimpleXMLElement $lineItemsXml = null,
		\SimpleXMLElement $consignmentXml = null
	) {
		$order = new static;
		$order->setId((string)$xml->salesorderCode);

		foreach ($xml->children() as $key => $value) {
			$order->{$key} = (string)$value;
		}

		if ($lineItemsXml) {
			foreach ($lineItemsXml->SalesOrderLine as $lineItem) {
				$order->getLineItems()->push(LineItem::fromXml($lineItem));
			}
		}

		if ($consignmentXml) {
			foreach ($consignmentXml->ConsignmentList as $consignment) {
				$order->getConsignments()->push(Consignment::fromXml($consignment));
			}
		}

		return $order;
	}
}