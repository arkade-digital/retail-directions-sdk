<?php

namespace Arkade\RetailDirections;

use Zend\Soap;
use Carbon\Carbon;
use Illuminate\Support\Fluent;
use Illuminate\Support\Collection;

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
     * History container for testing.
     *
     * @var Collection|null
     */
    protected $historyContainer;

    /**
     * Client constructor.
     *
     * @param string      $wsdl URL or path to WSDL file
     * @param Soap\Client $client
     */
    public function __construct($wsdl, Soap\Client $client = null)
    {
        $this->client = $client ?: $this->buildClient($wsdl);
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
     * Return history container for testing.
     *
     * @return Collection|null
     */
    public function getHistoryContainer()
    {
        return $this->historyContainer;
    }

    /**
     * Set history container for testing.
     *
     * @param  Collection|null $historyContainer
     * @return Client
     */
    public function setHistoryContainer(Collection $historyContainer)
    {
        $this->historyContainer = $historyContainer;

        return $this;
    }

    /**
     * Return auth module.
     *
     * @return Modules\Auth
     */
    public function auth()
    {
        return new Modules\Auth($this);
    }

    /**
     * Return users module.
     *
     * @return Modules\Users
     */
    public function users()
    {
        return new Modules\Users($this);
    }

    /**
     * Call the SOAP service and return XML result.
     *
     * @param  string $serviceName
     * @param  array  $attributes
     * @return mixed
     */
    public function call($serviceName, $attributes = [])
    {
        $this->client->addSoapInputHeader(
            $this->buildSecurityTokenHeader($serviceName)
        );

        $request = $this->buildRequestXml($serviceName, $attributes);

        try {
            $response = $this->client->call('RDService', [
                'RDService' => [
                    'request' => $request
                ]
            ]);
        } finally {
            if ($this->historyContainer) {
                $this->historyContainer->push(new Fluent([
                    'request'         => $this->client->getLastRequest(),
                    'requestHeaders'  => $this->client->getLastRequestHeaders(),
                    'response'        => $this->client->getLastResponse(),
                    'responseHeaders' => $this->client->getLastResponseHeaders()
                ]));
            }
        }

//        var_dump($response); die;
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
        return $this->client = new Soap\Client($wsdl);
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
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $child = $element->addChild($key);
                $this->arrayToXml($value, $child);
            } else {
                $element->addChild($key, htmlspecialchars($value));
            }
        }
    }
}
