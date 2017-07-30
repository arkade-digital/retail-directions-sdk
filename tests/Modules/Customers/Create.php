<?php

namespace Arkade\RetailDirections\Modules\Customers;

use Carbon\Carbon;
use Arkade\RetailDirections;
use Illuminate\Support\Collection;

trait Create
{
    public function testCreateSendsCorrectRequest()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerGetRequest',
            'Customers/CustomerGetFailedResponse'
        );

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerCreateWithIdRequest',
            'Customers/CustomerCreateSuccessResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $client->customers()->create(new RetailDirections\Customer(
            '100400000001',
            [
                'firstName'        => 'Malcolm',
                'lastName'         => 'Turnball',
                'emailAddress'     => 'malcolm.turnball@gov.au',
                'homeLocationCode' => '0202',
                'origin'           => 'Google'
            ]
        ), Carbon::parse('2017-07-25T06:45:55.448605+00:00'));
    }

    /**
     * @expectedException \Arkade\RetailDirections\Exceptions\ValidationException
     */
    public function testCreateThrowsValidationException()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerGetRequest',
            'Customers/CustomerGetFailedResponse'
        );

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerCreateWithIdRequest',
            'Customers/CustomerCreateFailedValidationResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $client->customers()->create(new RetailDirections\Customer(
            '100400000001',
            [
                'firstName'        => 'Malcolm',
                'lastName'         => 'Turnball',
                'emailAddress'     => 'malcolm.turnball@gov.au',
                'homeLocationCode' => '0202',
                'origin'           => 'Google'
            ]
        ), Carbon::parse('2017-07-25T06:45:55.448605+00:00'));
    }

    /**
     * @expectedException \Arkade\RetailDirections\Exceptions\AlreadyExistsException
     */
    public function testCreateThrowsAlreadyExistsExceptionForId()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerGetRequest',
            'Customers/CustomerGetSuccessResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $client->customers()->create(new RetailDirections\Customer(
            '100400000001',
            [
                'firstName'        => 'Malcolm',
                'lastName'         => 'Turnball',
                'emailAddress'     => 'malcolm.turnball@gov.au',
                'homeLocationCode' => '0202',
                'origin'           => 'Google'
            ]
        ), Carbon::parse('2017-07-25T06:45:55.448605+00:00'));
    }

    /**
     * @expectedException \Arkade\RetailDirections\Exceptions\AlreadyExistsException
     */
    public function testCreateThrowsAlreadyExistsExceptionForEmail()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerGetRequest',
            'Customers/CustomerGetFailedResponse'
        );

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerCreateWithIdRequest',
            'Customers/CustomerCreateFailedExistsResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $client->customers()->create(new RetailDirections\Customer(
            '100400000001',
            [
                'firstName'        => 'Malcolm',
                'lastName'         => 'Turnball',
                'emailAddress'     => 'malcolm.turnball@gov.au',
                'homeLocationCode' => '0202',
                'origin'           => 'Google'
            ]
        ), Carbon::parse('2017-07-25T06:45:55.448605+00:00'));
    }

    public function testCreateReturnsPopulatedCustomerEntity()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerGetRequest',
            'Customers/CustomerGetFailedResponse'
        );

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerCreateWithIdRequest',
            'Customers/CustomerCreateSuccessResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $customer = $client->customers()->create(new RetailDirections\Customer(
            '100400000001',
            [
                'firstName'        => 'Malcolm',
                'lastName'         => 'Turnball',
                'emailAddress'     => 'malcolm.turnball@gov.au',
                'homeLocationCode' => '0202',
                'origin'           => 'Google'
            ]
        ), Carbon::parse('2017-07-25T06:45:55.448605+00:00'));

        $this->assertInstanceOf(RetailDirections\Customer::class, $customer);

        $this->assertEquals('100400000001', $customer->getId());
        $this->assertEquals('Malcolm', $customer->firstName);
        $this->assertEquals('Turnball', $customer->lastName);
    }
}