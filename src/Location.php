<?php

namespace Arkade\RetailDirections;

use Illuminate\Support\Fluent;

class Location extends Fluent
{
    /**
     * Location constructor.
     *
     * @param string $id
     */
    public function __construct($id)
    {
        $attributes = [
            'homeLocationCode'  => $id,
            'locationCode'      => $id
        ];

        parent::__construct($attributes);
    }
}
