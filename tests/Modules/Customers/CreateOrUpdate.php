<?php

namespace Arkade\RetailDirections\Modules\Customers;

use Carbon\Carbon;
use Arkade\RetailDirections;
use Illuminate\Support\Collection;

trait CreateOrUpdate
{
    public function testCreateOrUpdateSendsCorrectRequest()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerCreateWithIdRequest',
            'Customers/CustomerCreateSuccessResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $client->customers()->createOrUpdate(new RetailDirections\Customer(
            '100400000001',
            [
                'firstName'        => 'Malcolm',
                'lastName'         => 'Turnball',
                'emailAddress'     => 'malcolm.turnball@gov.au',
                'homeLocationCode' => '0202',
                'origin'           => 'Google'
            ]
        ));
    }

    /**
     * @expectedException \Arkade\RetailDirections\Exceptions\ValidationException
     */
    public function testCreateOrUpdateThrowsValidationException()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerCreateWithIdRequest',
            'Customers/CustomerCreateFailedValidationResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $client->customers()->createOrUpdate(new RetailDirections\Customer(
            '100400000001',
            [
                'firstName'        => 'Malcolm',
                'lastName'         => 'Turnball',
                'emailAddress'     => 'malcolm.turnball@gov.au',
                'homeLocationCode' => '0202',
                'origin'           => 'Google'
            ]
        ));
    }

    /**
     * @expectedException \Arkade\RetailDirections\Exceptions\AlreadyExistsException
     */
    public function testCreateOrUpdateThrowsAlreadyExistsException()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerCreateWithIdRequest',
            'Customers/CustomerCreateFailedExistsResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $client->customers()->createOrUpdate(new RetailDirections\Customer(
            '100400000001',
            [
                'firstName'        => 'Malcolm',
                'lastName'         => 'Turnball',
                'emailAddress'     => 'malcolm.turnball@gov.au',
                'homeLocationCode' => '0202',
                'origin'           => 'Google'
            ]
        ));
    }

    public function testCreateOrUpdateReturnsPopulatedCustomerEntity()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerCreateWithIdRequest',
            'Customers/CustomerCreateSuccessResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $customer = $client->customers()->createOrUpdate(new RetailDirections\Customer(
            '100400000001',
            [
                'firstName'        => 'Malcolm',
                'lastName'         => 'Turnball',
                'emailAddress'     => 'malcolm.turnball@gov.au',
                'homeLocationCode' => '0202',
                'origin'           => 'Google'
            ]
        ));

        $this->assertInstanceOf(RetailDirections\Customer::class, $customer);

        $this->assertEquals('100400000001', $customer->getId());
        $this->assertEquals('Malcolm', $customer->firstName);
        $this->assertEquals('Turnball', $customer->lastName);
    }
}