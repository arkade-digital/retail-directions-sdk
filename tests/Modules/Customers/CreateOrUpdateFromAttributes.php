<?php

namespace Arkade\RetailDirections\Modules\Customers;

use Carbon\Carbon;
use Arkade\RetailDirections;
use Illuminate\Support\Collection;

trait CreateOrUpdateFromAttributes
{
    public function testCreateOrUpdateFromAttributesSendsCorrectRequest()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerCreateRequest',
            'Customers/CustomerCreateSuccessResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $client->customers()->createOrUpdateFromAttributes([
            'firstName'        => 'Malcolm',
            'lastName'         => 'Turnball',
            'emailAddress'     => 'malcolm.turnball@gov.au',
            'homeLocationCode' => '0202',
            'origin'           => 'Google'
        ]);
    }

    /**
     * @expectedException \Arkade\RetailDirections\Exceptions\ValidationException
     */
    public function testCreateOrUpdateFromAttributesThrowsValidationException()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerCreateRequest',
            'Customers/CustomerCreateFailedValidationResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $client->customers()->createOrUpdateFromAttributes([
            'firstName'        => 'Malcolm',
            'lastName'         => 'Turnball',
            'emailAddress'     => 'malcolm.turnball@gov.au',
            'homeLocationCode' => '0202',
            'origin'           => 'Google'
        ]);
    }

    /**
     * @expectedException \Arkade\RetailDirections\Exceptions\AlreadyExistsException
     */
    public function testCreateOrUpdateFromAttributesThrowsAlreadyExistsException()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerCreateRequest',
            'Customers/CustomerCreateFailedExistsResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $client->customers()->createOrUpdateFromAttributes([
            'firstName'        => 'Malcolm',
            'lastName'         => 'Turnball',
            'emailAddress'     => 'malcolm.turnball@gov.au',
            'homeLocationCode' => '0202',
            'origin'           => 'Google'
        ]);
    }

    public function testCreateOrUpdateFromAttributesReturnsPopulatedCustomerEntity()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerCreateRequest',
            'Customers/CustomerCreateSuccessResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $customer = $client->customers()->createOrUpdateFromAttributes([
            'firstName'        => 'Malcolm',
            'lastName'         => 'Turnball',
            'emailAddress'     => 'malcolm.turnball@gov.au',
            'homeLocationCode' => '0202',
            'origin'           => 'Google'
        ]);

        $this->assertInstanceOf(RetailDirections\Customer::class, $customer);

        $this->assertEquals('100400000001', $customer->getId());
        $this->assertEquals('Malcolm', $customer->firstName);
        $this->assertEquals('Turnball', $customer->lastName);
    }
}