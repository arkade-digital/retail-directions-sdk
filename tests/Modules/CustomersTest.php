<?php

namespace Arkade\RetailDirections\Modules;

use Carbon\Carbon;
use Arkade\RetailDirections;
use Illuminate\Support\Collection;

class CustomersTest extends RetailDirections\TestCase
{
    use Customers\CreateTrait;

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
            '100400000001',
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
            '100400000001',
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
            '100400000001',
            Carbon::parse('2017-07-25T06:45:55.448605+00:00')
        );

        $this->assertInstanceOf(RetailDirections\Customer::class, $customer);
        $this->assertEquals('100400000001', $customer->getId());
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
            '100400000001',
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

    public function testFindByEmailSendsCorrectRequest()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerGetByEmailRequest',
            'Customers/CustomerGetByEmailSuccessResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $client->customers()->findByEmail(
            'malcolm.turnball@gov.au',
            Carbon::parse('2017-07-28T04:42:44.191054+00:00')
        );
    }

    /**
     * @expectedException \Arkade\RetailDirections\Exceptions\NotFoundException
     */
    public function testFindByEmailThrowsNotFoundExceptionForMissingEmail()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerGetByEmailRequest',
            'Customers/CustomerGetByEmailFailedResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $client->customers()->findByEmail(
            'malcolm.turnball@gov.au',
            Carbon::parse('2017-07-28T04:42:44.191054+00:00')
        );
    }

    public function testFindByEmailReturnsPopulatedCustomerEntity()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerGetByEmailRequest',
            'Customers/CustomerGetByEmailSuccessResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $customers = $client->customers()->findByEmail(
            'malcolm.turnball@gov.au',
            Carbon::parse('2017-07-28T04:42:44.191054+00:00')
        );

        $this->assertInstanceOf(Collection::class, $customers);

        $this->assertEquals('100400000001', $customers->first()->getId());
        $this->assertEquals('Malcolm', $customers->first()->firstName);
        $this->assertEquals('Turnball', $customers->first()->lastName);
    }

    public function testFindByIdentificationSendsCorrectRequest()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/FindByIdentificationRequest',
            'Customers/FindByIdentificationSuccessResponse'
        );

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerGetRequest',
            'Customers/CustomerGetSuccessResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $client->customers()->findByIdentification(
            new RetailDirections\Identifications\Omneo('271DNTKT291290VD8H9WKO5QO0YR0000'),
            Carbon::parse('2017-07-25T06:45:55.448605+00:00')
        );
    }

    /**
     * @expectedException \Arkade\RetailDirections\Exceptions\NotFoundException
     */
    public function testFindByIdentificationThrowsNotFoundExceptionForMissingId()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/FindByIdentificationRequest',
            'Customers/FindByIdentificationFailedResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $client->customers()->findByIdentification(
            new RetailDirections\Identifications\Omneo('271DNTKT291290VD8H9WKO5QO0YR0000'),
            Carbon::parse('2017-07-25T06:45:55.448605+00:00')
        );
    }

    public function testFindByIdentificationReturnsCollectionOfPopulatedCustomerEntity()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/FindByIdentificationRequest',
            'Customers/FindByIdentificationSuccessResponse'
        );

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerGetRequest',
            'Customers/CustomerGetSuccessResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $customers = $client->customers()->findByIdentification(
            new RetailDirections\Identifications\Omneo('271DNTKT291290VD8H9WKO5QO0YR0000'),
            Carbon::parse('2017-07-25T06:45:55.448605+00:00')
        );

        $this->assertInstanceOf(Collection::class, $customers);

        $this->assertInstanceOf(RetailDirections\Customer::class, $customers->first());
        $this->assertEquals('100400000001', $customers->first()->getId());
        $this->assertEquals('MR MALCOLM', $customers->first()->firstName);
        $this->assertEquals('TURNBALL', $customers->first()->lastName);
        $this->assertEquals('12345678', $customers->first()->mobileNumber);
    }
}