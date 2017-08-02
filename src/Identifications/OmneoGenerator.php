<?php

namespace Arkade\RetailDirections\Identifications;

use Ramsey\Uuid\Uuid;

class OmneoGenerator
{
    /**
     * Generate a 32 character member UUID.
     *
     * @return Omneo
     */
    public function generate()
    {
        return new Omneo(
            strtoupper(str_pad(implode('', [
                '271',
                (string) Uuid::uuid4()->getInteger()->convertToBase(36),
            ]), 32, '0', STR_PAD_RIGHT))
        );
    }
}