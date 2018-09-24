<?php

namespace Arkade\RetailDirections;

use Exception;
use Illuminate\Support\Facades\Storage;
use Zend\Soap;
use Carbon\Carbon;
use Zend\Diactoros;
use UnexpectedValueException;
use Illuminate\Support\Fluent;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Arkade\HttpRecorder\Recorder;
use Arkade\HttpRecorder\Transaction;
use Arkade\HttpRecorder\Drivers\EloquentDriver;
use Arkade\HttpRecorder\Integrations\Laravel\TransactionFactory;
use Arkade\HttpRecorder\Integrations\Laravel\TransactionModel;

class Client
{
    /**
     * Client for SOAP transport.
     *
     * @var Soap\Client
     */
    protected $client;

    /**
     * Namespace for XML SOAP types.
     *
     * @var string
     */
    protected $namespace = 'http://www.retaildirections.com/';

    /**
     * Outgoing authentication credentials.
     *
     * @var Credentials
     */
    protected $credentials;

    /**
     * History container.
     *
     * @var HistoryContainer|null
     */
    protected $historyContainer;

    /**
     * Location.
     * @var Location
     */
    protected $location;

    /**
     * Client constructor.
     *
     * @param string $wsdl URL or path to WSDL file
     */
    public function __construct($wsdl)
    {
        $this->client = $this->buildClient($wsdl);
    }

    /**
     * Set client for SOAP communication.
     *
     * @param  Soap\Client $client
     * @return Client
     */
    public function setClient(Soap\Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Return outgoing authentication credentials.
     *
     * @return Credentials
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * Set outgoing authentication credentials.
     *
     * @param  Credentials $credentials
     * @return Client
     */
    public function setCredentials(Credentials $credentials)
    {
        $this->credentials = $credentials;

        return $this;
    }

    /**
     * Return history container.
     *
     * @return HistoryContainer|null
     */
    public function getHistoryContainer()
    {
        return $this->historyContainer;
    }

    /**
     * Set history container.
     *
     * @param  HistoryContainer|null $historyContainer
     * @return Client
     */
    public function setHistoryContainer(HistoryContainer $historyContainer = null)
    {
        $this->historyContainer = $historyContainer;

        return $this;
    }

    /**
     * Return customers module.
     *
     * @return Modules\Orders
     */
    public function orders()
    {
        return new Modules\Orders($this);
    }

    /**
     * Return customers module.
     *
     * @return Modules\Customers
     */
    public function customers()
    {
        return new Modules\Customers($this);
    }

    /**
     * Return items module.
     *
     * @return Modules\Items
     */
    public function items()
    {
        return new Modules\Items($this);
    }

    /**
     * Return loyalty customers module.
     *
     * @return Modules\LoyaltyCustomers
     */
    public function loyaltyCustomers()
    {
        return new Modules\LoyaltyCustomers($this);
    }

    /**
     * Return stores module.
     *
     * @return Modules\Stores
     */
    public function stores()
    {
        return new Modules\Stores($this);
    }

    /**
     * Set the location.
     *
     * @param Location $location
     * @return Client
     */
    public function setLocation(Location $location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Add location attributes to an array.
     *
     * @param array $attributes
     * @return array
     */
    protected function withLocationAttributes(array $attributes)
    {
        return array_map(function ($value) {
            if ($this->location) return array_merge($value, $this->location->toArray());
            return $value;
        }, $attributes);
    }

    /**
     * Get the location.
     *
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Build the request attributes.
     *
     * @param array $attributes
     * @return array
     */
    protected function buildRequestAttributes(array $attributes)
    {
        return $this->withLocationAttributes($attributes);
    }

    /**
     * Call the SOAP service and return XML result.
     *
     * @param  string $serviceName
     * @param  array  $attributes
     * @return mixed
     * @throws Exceptions\ServiceException
     */
    public function call($serviceName, $attributes = [])
    {
        $this->client->addSoapInputHeader(
            $this->buildSecurityTokenHeader($serviceName)
        );

        $request = $this->buildRequestXml(
            $serviceName,
            $this->buildRequestAttributes($attributes)
        );

        try {

            $response = $this->client->call('RDService', [
                'RDService' => [
                    'request' => $request
                ]
            ]);
        } catch (\Exception $e) {
            $this->persistHistory($request, null, $e);
            throw $e;
        }

        $this->persistHistory($request, $response->RDServiceResult);

        $response = $this->parseResponseXml($response->RDServiceResult);

        if ($response->ErrorResponse) {
            throw (new Exceptions\ServiceException(
                (string) $response->ErrorResponse->errorMessage,
                (int) $response->ErrorResponse->errorNumber
            ))->setHistoryContainer($this->historyContainer);
        }

        return $response;
    }

    /**
     * Format provided timestamp as 2008-05-20T09:47:47.9161528+10:00.
     *
     * @param  Carbon $timestamp
     * @return string
     */
    public function formatDateTime(Carbon $timestamp)
    {
        return $timestamp->format('Y-m-d\TH:i:s.uP');
    }

    /**
     * Build SOAP client in WSDL mode.
     *
     * @param  string $wsdl
     * @return Soap\Client
     */
    protected function buildClient($wsdl)
    {
        return $this->client = new Soap\Client($wsdl, [
            'user_agent' => 'PHPSoapClient'
        ]);
    }

    /**
     * Return SecurityToken SOAP header for requests.
     *
     * @param  string $serviceName
     * @return \SoapHeader
     */
    protected function buildSecurityTokenHeader($serviceName)
    {
        return new \SoapHeader(
            $this->namespace,
            'SecurityToken',
            [
                'Username'       => $this->credentials ? $this->credentials->getUsername() : '',
                'Password'       => $this->credentials ? $this->credentials->getPassword() : '',
                'ServiceName'    => $serviceName,
                'ServiceVersion' => '1',
            ]
        );
    }

    /**
     * Build request XML and return as a SimpleXMLElement.
     *
     * @param  string $serviceName
     * @param  array  $attributes
     * @return \SimpleXMLElement
     */
    protected function buildRequestXml($serviceName, array $attributes = [])
    {
        $request = new \SimpleXMLElement($this->buildInitialEnvelopeString($serviceName));

        $this->arrayToXml($attributes, $request);

        return substr($request->asXML(), 22, -1);
    }

    /**
     * Return an XML string to represent initial request envelope.
     *
     * @param  string $serviceName
     * @return string
     */
    protected function buildInitialEnvelopeString($serviceName)
    {
        return <<<EOT
<{$serviceName}Request
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns="$this->namespace"></{$serviceName}Request>
EOT;
    }

    /**
     * Append provided array to the given XML element.
     *
     * @param array $data
     * @param \SimpleXMLElement $element
     */
    protected function arrayToXml(array $data, \SimpleXMLElement &$element)
    {
        foreach ($data as $key => $value)
        {
            if (is_array($value)) {

                if (! empty($value['@node'])) {
                    $key = $value['@node'];
                    unset($value['@node']);
                }

                $child = $element->addChild($key);
                $this->arrayToXml($value, $child);

                continue;

            }

            $element->addChild($key, htmlspecialchars($value));
        }
    }

    /**
     * Parse the response XML string.
     *
     * @param  string $response
     * @return \SimpleXMLElement
     */
    protected function parseResponseXml($response)
    {
        return new \SimpleXMLElement($response);
    }

    /**
     * Persist last request into history container.
     *
     * @param  string         $serviceRequest XML string from SOAP service request
     * @param  string|null    $serviceResult  XML string from SOAP service result
     * @param  Exception|null $exception
     * @return void
     */
    protected function persistHistory($serviceRequest, $serviceResult = null, Exception $exception = null)
    {
        if (! $this->historyContainer) return;

        $history = new Fluent([
            'request'         => $this->buildLastRequest(),
            'requestHeaders'  => html_entity_decode($this->client->getLastRequestHeaders()),
            'requestBody'     => html_entity_decode($this->client->getLastRequest()),
            'serviceRequest'  => html_entity_decode($serviceRequest),
            'response'        => $this->buildLastResponse(),
            'responseHeaders' => html_entity_decode($this->client->getLastResponseHeaders()),
            'responseBody'    => html_entity_decode($this->client->getLastResponse()),
            'serviceResult'   => $serviceResult ? substr($serviceResult, 3) : null, // Trim weird characters from beginning
            'exception'       => $exception
        ]);

        $this->historyContainer->record($history);

        if($history->get('request')){
            $recorder = new Recorder((new EloquentDriver(new TransactionModel(), new TransactionFactory())));
            $transaction = new Transaction();
            $transaction->setRequest($this->buildLastRequest());
            $transaction->setResponse($this->buildLastResponse());
            $transaction->pushTags('retail-directions','outgoing');

            if($exception){
                $transaction->setException($exception);
            }

            $recorder->record($transaction);
        }
    }

    /**
     * Build PSR-7 compatible instance for last request.
     *
     * @return RequestInterface|null
     */
    protected function buildLastRequest()
    {
        try {
            return Diactoros\Request\Serializer::fromString(implode('', [
                html_entity_decode($this->client->getLastRequestHeaders()),
                html_entity_decode($this->client->getLastRequest())
            ]));
        } catch (UnexpectedValueException $e) {
            return null;
        }
    }

    /**
     * Build PSR-7 compatible instance for last response.
     *
     * @return ResponseInterface|null
     */
    protected function buildLastResponse()
    {
        try {
            return Diactoros\Response\Serializer::fromString(implode('', [
                html_entity_decode($this->client->getLastResponseHeaders()),
                "\r\n",
                html_entity_decode($this->client->getLastResponse())
            ]));
        } catch (UnexpectedValueException $e) {
            return null;
        }
    }
}
