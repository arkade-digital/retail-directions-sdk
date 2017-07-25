<?php

namespace Arkade\RetailDirections;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Collection;

class ClientTest extends TestCase
{
    public function testSetCredentials()
    {
        $client = new Client('https://api.example.com');

        $client->setCredentials(new Credentials('abc123', 'secret'));

        $this->assertInstanceOf(Credentials::class, $client->getCredentials());
    }

    public function testSetCredentialsIsChainable()
    {
        $client = new Client('https://api.example.com');

        $chainable = $client->setCredentials(new Credentials('abc123', 'secret'));

        $this->assertInstanceOf(Client::class, $chainable);
    }

    public function testFormatDateTimeReturnsCorrectFormat()
    {
        $client = new Client('https://api.example.com');

        $timestamp = Carbon::parse('2017-12-25 15:30:20');

        $this->assertEquals('2017-12-25T15:30:20.000000+00:00', $client->formatDateTime($timestamp));
    }

    public function testSOAPEnvelopeCorrectlyFormatted()
    {
        $history = new Collection;

        $client = (new Client(__DIR__.'/Stubs/wsdl.xml'))
            ->setCredentials(new Credentials('abc123', 'secret'))
            ->setHistoryContainer($history);

        try {
            $client->call('CustomerGet');
        } catch (\SoapFault $e) {
            //
        }

        $this->assertEquals(
            file_get_contents(__DIR__.'/Stubs/Customers/CustomerGetRequest.xml'),
            $history->first()->request
        );
    }
}