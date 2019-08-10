<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Fluent;

class CustomerLoyalty extends Fluent
{
    protected $id;

    public function __construct($id, $attributes = [])
    {
        parent::__construct($attributes);
        $this->id = $id;

    }

    public static function fromXml(
        \SimpleXMLElement $xml

    ) {

        $loyalty = new static((string) $xml->loyaltyCustomerId);

        foreach ($xml->children() as $key => $value) {
            $loyalty->{$key} = (string) $value;
        }

        return $loyalty;
    }
}