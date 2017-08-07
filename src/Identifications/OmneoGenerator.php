<?php

namespace Arkade\RetailDirections\Identifications;

use Ramsey\Uuid\Uuid;
use SKleeschulte\Base32;

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
            strtoupper(implode('', [
                '271',
                str_pad(
                    (string) Base32::encodeIntStrToCrockford((string) Uuid::uuid4()->getInteger()),
                    29, '0', STR_PAD_LEFT
                )
            ]))
        );
    }
}