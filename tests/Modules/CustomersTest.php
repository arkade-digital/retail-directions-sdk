<?php

namespace Arkade\RetailDirections\Modules;

use Carbon\Carbon;
use Arkade\RetailDirections;
use Illuminate\Support\Collection;

class CustomersTest extends RetailDirections\TestCase
{
    public function testFindByIdSendsCorrectRequest()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP($soapClient, 'Customers/CustomerGetRequest');

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $client->customers()->findById(
            'ABC123',
            Carbon::parse('2017-07-25T06:45:55.448605+00:00')
        );
    }

    /**
     * @expectedException \Arkade\RetailDirections\Exceptions\NotFoundException
     */
    public function testFindByIdReturnsNullForMissingId()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerGetRequest',
            'Customers/CustomerGetFailedResponse'
        );

        $client = (new RetailDirections\Client($this->sandboxWSDL()))->setClient($soapClient);

        $client->customers()->findById(
            'ABC123',
            Carbon::parse('2017-07-25T06:45:55.448605+00:00')
        );
    }

//    public function testFindByIdReturnsNullForMissingId()
//    {
//        $history = new Collection;
//
//        $client = (new RetailDirections\Client($this->sandboxWSDL()))
//            ->setCredentials(new RetailDirections\Credentials('arkade', '1422'))
//            ->setHistoryContainer($history);
//
//        $client->customers()->findById('ABC123');
//
//        file_put_contents(
//            __DIR__.'/../Stubs/Customers/CustomerGetFailedResponse.xml',
//            $history->first()->serviceResult
//        );
//
//        var_dump($history->first()); die;
//    }
}