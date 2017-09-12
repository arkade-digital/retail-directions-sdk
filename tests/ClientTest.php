<?php

namespace Arkade\RetailDirections;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class ClientTest extends TestCase
{
    public function testSetCredentials()
    {
        $client = new Client($this->mockWSDL());

        $client->setCredentials(new Credentials('abc123', 'secret'));

        $this->assertInstanceOf(
            Credentials::class,
            $client->getCredentials()
        );
    }

    public function testSetLocation()
    {
        $client = new Client($this->mockWSDL());

        $client->setLocation(new Location('123'));

        $this->assertInstanceOf(
            Location::class,
            $client->getLocation()
        );
    }

    public function testSetCredentialsIsChainable()
    {
        $client = new Client($this->mockWSDL());

        $chainable = $client->setCredentials(new Credentials('abc123', 'secret'));

        $this->assertInstanceOf(Client::class, $chainable);
    }

    public function testFormatDateTimeReturnsCorrectFormat()
    {
        $client = new Client($this->mockWSDL());

        $timestamp = Carbon::parse('2017-12-25 15:30:20');

        $this->assertEquals(
            '2017-12-25T15:30:20.000000+00:00',
            $client->formatDateTime($timestamp)
        );
    }

    public function testSOAPRequestCorrectlyFormatted()
    {
        $history = new HistoryContainer;

        $client = (new Client($this->mockWSDL()))
            ->setCredentials(new Credentials('abc123', 'secret'))
            ->setHistoryContainer($history);

        try {
            $client->customers()->findById(
                '100400000001',
                Carbon::parse('2017-07-25T06:45:55.448605+00:00')
            );
        } catch (\SoapFault $e) {
            //
        }

        $this->assertEquals(
            file_get_contents(__DIR__.'/Stubs/FullRequestEnvelope.xml'),
            $history->first()->requestBody
        );
    }
}