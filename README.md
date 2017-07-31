# Retail Directions PHP SDK

This SDK provides simple access to the SOAP based Retail Directions Web Service (RDWS). It handles most associated complexities including authentication, entity abstraction, errors and more.

## Contents

- [Getting started](#getting-started)
- [Prerequisites](#prerequisites)
- [Creating a client](#creating-a-client)
- [Integrating with Laravel](#integrating-with-laravel)
- [Available methods](#available-methods)
- [Recording history](#recording-history)
- [Contributing](#contributing)

## Getting started

Install the SDK into your project using Composer.

```bash
composer config repositories.retail-directions-sdk git git@github.com:arkade-digital/retail-directions-sdk.git
composer require arkade/retail-directions-sdk
```

## Prerequisites

Before sending requests to Retail Directions, you will need a username and password.

## Creating a client

> If you are using Laravel, skip to the [Integrating with Laravel](#integrating-with-laravel) section

To begin using the SDK, you will first need to create an authenticated client with the information you have obtained above.

```php
use Arkade\RetailDirections;

$client = (new RetailDirections\Client('http://59.154.22.13/RDWS/RDWS.asmx?WSDL'))
    ->setCredentials(new RetailDirections\Credentials('YOUR_USERNAME', 'YOUR_PASSWORD'));
```

You do not have to set credentials on the client. Some sandbox environments allow unauthenticated access. If you don't set credentials, all requests will be sent without authentication headers. In a production environment, this will most likely result in an unauthorized response.

## Integrating with Laravel

This package ships with a Laravel specific service provider which allows you to set your credentials from your configuration file and environment.

### Registering the provider

Add the following to the `providers` array in your `config/app.php` file.

```php
Arkade\RetailDirections\LaravelServiceProvider::class
```

### Adding config keys

In your `config/services.php` file, add the following to the array.

```php
'retaildirections' => [
    'wsdl'     => env('RETAILDIRECTIONS_WSDL'),
    'username' => env('RETAILDIRECTIONS_USERNAME'),
    'password' => env('RETAILDIRECTIONS_PASSWORD'),
]
```

### Adding environment keys

In your `.env` file, add the following keys.

```ini
RETAILDIRECTIONS_WSDL=
RETAILDIRECTIONS_USERNAME=
RETAILDIRECTIONS_PASSWORD=
```

### Resolving a client

To resolve a fully authenticated client, you simply pull it from the service container. This can be done in a few ways.

#### Type hinting

```php
use Arkade\RetailDirections\Client;

public function yourControllerMethod(Client $client) {
    // Call methods on $client
}
```

#### Using the `app()` helper

```php
use Arkade\RetailDirections\Client;

public function anyMethod() {
    $client = app(Client::class);
    // Call methods on $client
}
```

## Available methods

### Customers module

#### `findById($id)`

When provided a valid Retail Directions ID, this method will return an `Arkade\RetailDirections\Customer` instance containing all the attributes that Retail Directions holds for this customer.

If the provided ID is invalid, this method will throw `Arkade\RetailDirections\Exceptions\NotFoundException` which you should handle in your application.

```php
$client->customers()->findById('123456');
```

#### `findByIdentification(Identification $identification)`

When provided a valid `Arkade\RetailDirections\Identification` instance, this method will return an `Arkade\RetailDirections\Customer` instance containing all the attributes that Retail Directions holds for this customer.

If the provided identification is invalid, this method will throw `Arkade\RetailDirections\Exceptions\NotFoundException` which you should handle in your application.

```php
$client->customers()->findByIdentificiation(new Identifications\OmneoMemberId('abc123'));
```

#### `findByEmail($email)`

When provided a valid email address, this method will return a `Illuminate\Support\Collection` instance containing `Arkade\RetailDirections\Customer` instances that have the provided email address.

If no customers with this email address are located, this method will throw `Arkade\RetailDirections\Exceptions\NotFoundException` which you should handle in your application.

```php
$client->customers()->findByEmail('foo@example.com');
```

#### `create(Customer $customer)`

Create a Retail Directions customer.

To utilise this method, you first need to instantiate a `Arkade\RetailDirections\Customer` instance with your desired attributes. Once instantiated, you pass this instance into the `create()` method.

If you set a Retail Directions customer ID using `$customer->setId('foo')`, the customer will be created with the provided ID. If you do not set an ID, Retail Directions will generate one for you.

You can also set relevant identifications on the user before passing into the create method. You should use `$customer->addIdentification()` before passing into the `create()` method.

At the minimum, you must provide the fields in the example below for Retail Directions to create the customer. If you do not provide all required fields, an `Arkade\RetailDirections\Exceptions\ValidationException` will be thrown.

If your provided customer ID, identification or email address already exists for another customer, an `Arkade\RetailDirections\Exceptions\AlreadyExistsException` will be thrown.

Please consult your Retail Directions integration notes for information on which fields you will need to provide for your integration.

```php
use Arkade\RetailDirections\Customer;

$customer = new Customer('1234', [
    'firstName'        => 'Malcolm',
    'lastName'         => 'Turnball',
    'emailAddress'     => 'malcolm.turnball@gov.au',
    'homeLocationCode' => '1004',
    'origin'           => 'Google'
]);

$client->customers()->create($customer);
```

#### `update(Customer $customer)`

Update attributes for customer with the given ID.

To utilise this method, you first need to instantiate a `Arkade\RetailDirections\Customer` instance with your desired ID and attributes. Once instantiated, you pass this instance into the `update()` method.

If your attributes do not pass validation at Retail Directions, an `Arkade\RetailDirections\Exceptions\ValidationException` will be thrown.

If the provided customer ID does not exist, an `Arkade\RetailDirections\Exceptions\NotFoundException` will be thrown.

Please consult your Retail Directions integration notes for information on which fields you will need to provide for your integration.

```php
use Arkade\RetailDirections\Customer;

$customer = (new Customer([
    'lastName' => 'Trumbell'
]))->setId('1234');

$client->customers()->update($customer);
```

## Recording history

To debug your SDK requests and to assist in building integrations for new endpoints, you may use the history container. When provided an Illuminate collection, the SDK will record all requests and responses into the container.

> If you are using the Laravel service provider, a history container will already be set for you
>
> You may access it using `$client->getHistoryContainer()` anywhere in your application

```php
use Arkade\RetailDirections;
use Illuminate\Support\Collection;

$history = new Collection;

$client = (new RetailDirections\Client('http://59.154.22.13/RDWS/RDWS.asmx?WSDL'))
    ->setHistoryContainer($history);
    
$client->doSomething();

var_dump($history->first);

[
    'request'         => '...', // Full XML SOAP request
    'requestHeaders'  => '...', // SOAP security headers
    'serviceRequest'  => '...', // Internal RDWS XML service request
    'response'        => '...', // Full XML SOAP response
    'responseHeaders' => '...', // SOAP security headers
    'serviceResult'   => '...'  // Internal RDWS XML service response
]
```

If you have enabled history collection, any thrown exceptions will also contain the collection for easily debugging what went wrong.

```php
use Arkade\RetailDirections;
use Illuminate\Support\Collection;

$history = new Collection;

$client = (new RetailDirections\Client('http://59.154.22.13/RDWS/RDWS.asmx?WSDL'))
    ->setHistoryContainer($history);
    
try {
    $client->doSomething();
} catch (RetailDirections\Exceptions\ServiceException $e) {
    var_dump($e->getHistoryContainer()); // Illuminate\Support\Collection
}
```

## Contributing

If you wish to contribute to this library, please submit a pull request and assign to a member of Capcom for review.

All public methods should be accompanied with unit tests.

### Testing

```bash
./vendor/bin/phpunit
```