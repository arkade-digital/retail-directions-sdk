<?php

namespace Arkade\RetailDirections\Modules\Customers;

use Carbon\Carbon;
use Arkade\RetailDirections;

trait Create
{
    public function testCreateSendsCorrectRequest()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerCreateRequest',
            'Customers/CustomerCreateSuccessResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $client->customers()->create(new RetailDirections\Customer([
            'firstName'        => 'Malcolm',
            'lastName'         => 'Turnball',
            'emailAddress'     => 'malcolm.turnball@gov.au',
            'homeLocationCode' => '0202',
            'origin'           => 'Google'
        ]));
    }

    public function testCreateWithIdSendsCorrectRequest()
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

        $client->customers()->create(
            (new RetailDirections\Customer(
                [
                    'firstName'        => 'Malcolm',
                    'lastName'         => 'Turnball',
                    'emailAddress'     => 'malcolm.turnball@gov.au',
                    'homeLocationCode' => '0202',
                    'origin'           => 'Google'
                ]
            ))->setId('100400000001'),
            Carbon::parse('2017-07-25T06:45:55.448605+00:00')
        );
    }

    public function testCreateWithIdentificationsSendsCorrectRequest()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CreateWithIdentificationsRequest',
            'Customers/CreateWithIdentificationsSuccessResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $customer = new RetailDirections\Customer([
            'firstName'        => 'Malcolm',
            'lastName'         => 'Turnball',
            'emailAddress'     => 'malcolm.turnball@gov.au',
            'homeLocationCode' => '1004',
            'origin'           => 'Google'
        ]);

        $customer->pushIdentification(new RetailDirections\Identifications\Omneo('271DNTKT291290VD8H9WKO5QO0YR0000'));

        $client->customers()->create($customer);
    }

    public function testCreateWithIdentificationsReturnsPopulatedCustomerEntity()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CreateWithIdentificationsRequest',
            'Customers/CreateWithIdentificationsSuccessResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $customer = new RetailDirections\Customer([
            'firstName'        => 'Malcolm',
            'lastName'         => 'Turnball',
            'emailAddress'     => 'malcolm.turnball@gov.au',
            'homeLocationCode' => '1004',
            'origin'           => 'Google'
        ]);

        $customer->pushIdentification(new RetailDirections\Identifications\Omneo('271DNTKT291290VD8H9WKO5QO0YR0000'));

        $client->customers()->create($customer);

        $this->assertCount(1, $customer->getIdentifications());
        $this->assertEquals('OMNEO', $customer->getIdentifications()->first()->getType());
        $this->assertEquals('271DNTKT291290VD8H9WKO5QO0YR0000', $customer->getIdentifications()->first()->getValue());
    }

    /**
     * @expectedException \Arkade\RetailDirections\Exceptions\ValidationException
     */
    public function testCreateThrowsValidationException()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerCreateRequest',
            'Customers/CustomerCreateFailedValidationResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $client->customers()->create(new RetailDirections\Customer([
            'firstName'        => 'Malcolm',
            'lastName'         => 'Turnball',
            'emailAddress'     => 'malcolm.turnball@gov.au',
            'homeLocationCode' => '0202',
            'origin'           => 'Google'
        ]));
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

        $client->customers()->create(
            (new RetailDirections\Customer([
                'firstName'        => 'Malcolm',
                'lastName'         => 'Turnball',
                'emailAddress'     => 'malcolm.turnball@gov.au',
                'homeLocationCode' => '0202',
                'origin'           => 'Google'
            ]))->setId('100400000001'),
            Carbon::parse('2017-07-25T06:45:55.448605+00:00')
        );
    }

    /**
     * @expectedException \Arkade\RetailDirections\Exceptions\AlreadyExistsException
     */
    public function testCreateThrowsAlreadyExistsExceptionForEmail()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerCreateRequest',
            'Customers/CustomerCreateFailedExistsResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $client->customers()->create(new RetailDirections\Customer([
            'firstName'        => 'Malcolm',
            'lastName'         => 'Turnball',
            'emailAddress'     => 'malcolm.turnball@gov.au',
            'homeLocationCode' => '0202',
            'origin'           => 'Google'
        ]));
    }

    public function testCreateReturnsPopulatedCustomerEntity()
    {
        $soapClient = $this->mockSoapClient();

        $this->expectSOAP(
            $soapClient,
            'Customers/CustomerCreateRequest',
            'Customers/CustomerCreateSuccessResponse'
        );

        $client = (new RetailDirections\Client($this->mockWSDL()))->setClient($soapClient);

        $customer = $client->customers()->create(new RetailDirections\Customer([
            'firstName'        => 'Malcolm',
            'lastName'         => 'Turnball',
            'emailAddress'     => 'malcolm.turnball@gov.au',
            'homeLocationCode' => '0202',
            'origin'           => 'Google'
        ]));

        $this->assertInstanceOf(RetailDirections\Customer::class, $customer);

        $this->assertEquals('100400000001', $customer->getId());
        $this->assertEquals('Malcolm', $customer->firstName);
        $this->assertEquals('Turnball', $customer->lastName);
    }
}