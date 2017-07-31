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
//        $soapClient = $this->mockSoapClient();

        $history = new \Illuminate\Support\Collection;

//        $this->expectSOAP(
//            $soapClient,
//            'Customers/CustomerCreateWithIdentificationsRequest',
//            'Customers/CustomerCreateSuccessResponse'
//        );

        $client = (new RetailDirections\Client($this->sandboxWSDL()))
            ->setHistoryContainer($history);

        $customer = new RetailDirections\Customer([
            'firstName'        => 'Dan',
            'lastName'         => 'Greaves',
            'emailAddress'     => 'dan@arkade.com.au',
            'homeLocationCode' => '1004',
            'origin'           => 'Google'
        ]);

        $customer->pushIdentification(new RetailDirections\Identifications\Omneo('abc123'));

//        var_dump($customer); die;

        try {
            $client->customers()->create($customer);
        } catch (\Exception $e) {
//            var_dump($e->getHistoryContainer()); die;
        }

        file_put_contents(
            __DIR__.'/../../Stubs/Customers/CreateWithIdentificationsRequest.xml',
            $history->first()->request
        );

        file_put_contents(
            __DIR__.'/../../Stubs/Customers/CreateWithIdentificationsSuccessResponse.xml',
            $history->first()->response
        );

        var_dump($history); die;
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