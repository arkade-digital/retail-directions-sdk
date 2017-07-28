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

//    public function testUpdateSuccess()
//    {
//        $history = new Collection;
//
//        $client = (new RetailDirections\Client($this->sandboxWSDL()))
//            ->setHistoryContainer($history);
//
//        try {
//            $client->customers()->create([
//                'customerId'       => '100400000004',
//                'firstName'        => 'Dan',
//                'lastName'         => 'Greaves',
//                'emailAddress'     => 'dan+test2@arkade.com.au',
//                'homeLocationCode' => '1004',
//                'origin'           => 'Google'
//            ]);
//        } catch (\Exception $e) {
//            //
//        }
//
////        file_put_contents(
////            __DIR__.'/../Stubs/Customers/CustomerCreateFailedExistsResponse.xml',
////            $history->first()->serviceResult
////        );
//
//        var_dump($history->first()->serviceResult); die;
//    }
}