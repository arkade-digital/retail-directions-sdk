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
            '050101000005',
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
            '050101000005',
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
            '050101000005',
            Carbon::parse('2017-07-25T06:45:55.448605+00:00')
        );

        $this->assertInstanceOf(RetailDirections\Customer::class, $customer);
        $this->assertEquals('050101000005', $customer->getId());
        $this->assertEquals('MR MALCOLM', $customer->firstName);
        $this->assertEquals('TURNBALL', $customer->lastName);
        $this->assertEquals('12345678', $customer->mobileNumber);
    }

    public function testFindByIdReturnsPopulatedCustomerAddressCollection()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerGetRequest',
            'Customers/CustomerGetSuccessResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $customer = $client->customers()->findById(
            '050101000005',
            Carbon::parse('2017-07-25T06:45:55.448605+00:00')
        );

        $this->assertInstanceOf(Collection::class, $customer->getAddresses());
        $this->assertCount(2, $customer->getAddresses());

        $this->assertInstanceOf(RetailDirections\Address::class, $customer->getAddresses()->get(0));

        $this->assertEquals('B00  0170747664326700001', $customer->getAddresses()->get(0)->getId());
        $this->assertEquals('BILLING', $customer->getAddresses()->get(0)->addressNatureRef);
        $this->assertEquals('MR MALCOLM TURNBALL', $customer->getAddresses()->get(0)->addressee);
        $this->assertEquals('1 PARLIAMENT DR', $customer->getAddresses()->get(0)->address1);
        $this->assertEquals('CANBERRA', $customer->getAddresses()->get(0)->suburb);

        $this->assertInstanceOf(RetailDirections\Address::class, $customer->getAddresses()->get(1));
        $this->assertEquals('H00  0170747664327300000', $customer->getAddresses()->get(1)->getId());
        $this->assertEquals('DELIVERY', $customer->getAddresses()->get(1)->addressNatureRef);
        $this->assertEquals('MR MALCOLM TURNBALL', $customer->getAddresses()->get(1)->addressee);
        $this->assertEquals('1 PARLIAMENT DR', $customer->getAddresses()->get(1)->address1);
        $this->assertEquals('CANBERRA', $customer->getAddresses()->get(1)->suburb);
    }
}