<?php

namespace Arkade\RetailDirections\Identifications;

use Arkade\RetailDirections;

class OmneoGeneratorTest extends RetailDirections\TestCase
{
    /**
     * @test
     */
    public function generated_ids_start_with_271000()
    {
        $generator = new RetailDirections\Identifications\OmneoGenerator;

        for ($i = 1; $i <= 10; $i++) {
            $this->assertStringStartsWith('271000', $generator->generate()->getValue());
        }
    }

    /**
     * @test
     */
    public function generated_ids_exactly_32_chars_long()
    {
        $generator = new RetailDirections\Identifications\OmneoGenerator;

        for ($i = 1; $i <= 10; $i++) {
            $this->assertEquals(32, strlen($generator->generate()->getValue()));
        }
    }
}