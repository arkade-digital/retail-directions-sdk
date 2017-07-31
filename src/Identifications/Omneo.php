<?php

namespace Arkade\RetailDirections\Identifications;

use Arkade\RetailDirections\Identification;

class Omneo extends Identification
{
    /**
     * OmneoMemberId constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        parent::__construct('OMNEO', $value);
    }
}