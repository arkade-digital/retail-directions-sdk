<?php

namespace Arkade\RetailDirections;

use \Mockery;
use Zend\Soap;

trait Mocks
{
    /**
     * Return a mocked Zend SOAP client.
     *
     * @return Mockery\MockInterface
     */
    protected function mockSOAPClient()
    {
        $mock = Mockery::mock(Soap\Client::class);

        $mock
            ->shouldReceive('addSoapInputHeader')
            ->zeroOrMoreTimes();

        return $mock;
    }

    /**
     * Add transaction expectation to provided SOAP client mock.
     *
     * @param  Mockery\MockInterface $mock
     * @param  string                $requestStub  Relative path to XML request stub
     * @param  string|null           $responseStub Relative path to XML response stub
     * @return Mockery\MockInterface
     */
    protected function expectSOAP(
        Mockery\MockInterface $mock,
        $requestStub,
        $responseStub = null
    ) {
        $mock
            ->shouldReceive('call')
            ->once()
            ->with('RDService', $this->mockSOAPClientRequest($requestStub))
            ->andReturn($this->mockSOAPClientResponse($responseStub));

        return $mock;
    }

    /**
     * Return a mocked request for the SOAP client call() method.
     *
     * @param  string $requestStub Relative path to XML request stub
     * @return array
     */
    protected function mockSOAPClientRequest($requestStub)
    {
        return ['RDService' => ['request' => file_get_contents(__DIR__.'/Stubs/'.$requestStub.'.xml')]];
    }

    /**
     * Return a mocked response for the SOAP client call() method.
     *
     * @param  string|null $responseStub Relative path to XML response stub
     * @return \stdClass
     */
    protected function mockSOAPClientResponse($responseStub = null)
    {
        $response = new \stdClass();

        $response->RDServiceResult = $responseStub
            ? file_get_contents(__DIR__.'/Stubs/'.$responseStub.'.xml')
            : '<empty></empty>';

        return $response;
    }

    /**
     * Return path to mock WSDL file.
     *
     * @return string
     */
    protected function mockWSDL()
    {
        return __DIR__.'/Stubs/wsdl.xml';
    }

    /**
     * Return path to real sandbox WSDL file.
     *
     * Use this for making real requests to the sandbox environment.
     *
     * @return string
     */
    protected function sandboxWSDL()
    {
        return 'http://59.154.22.13/RDWS/RDWS.asmx?WSDL';
    }
}