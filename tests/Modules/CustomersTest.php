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

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerGetRequest',
            'Customers/CustomerGetSuccessResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $client->customers()->findById(
            'ABC123',
            Carbon::parse('2017-07-25T06:45:55.448605+00:00')
        );
    }

    /**
     * @expectedException \Arkade\RetailDirections\Exceptions\NotFoundException
     */
    public function testFindByIdThrowsNotFoundExceptionForMissingId()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerGetRequest',
            'Customers/CustomerGetFailedResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $client->customers()->findById(
            'ABC123',
            Carbon::parse('2017-07-25T06:45:55.448605+00:00')
        );
    }

    public function testFindByIdReturnsPopulatedCustomerEntity()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerGetRequest',
            'Customers/CustomerGetSuccessResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $customer = $client->customers()->findById(
            'ABC123',
            Carbon::parse('2017-07-25T06:45:55.448605+00:00')
        );

        $this->assertInstanceOf(RetailDirections\Customer::class, $customer);
    }

//    public function testFindByIdReturnsPopulatedUserEntity()
//    {
//        $history = new Collection;
//
//        $client = (new RetailDirections\Client($this->sandboxWSDL()))
//            ->setCredentials(new RetailDirections\Credentials('arkade', '1422'))
//            ->setHistoryContainer($history);
//
//        $customer = $client->customers()->findById('050101000005');
//
//        file_put_contents(
//            __DIR__.'/../Stubs/Customers/CustomerGetSuccessResponse.xml',
//            $history->first()->serviceResult
//        );
//
//        var_dump($history->first()->serviceResult); die;
//    }
}