# Retail Directions PHP SDK

This SDK provides simple access to the SOAP based Retail Directions Web Service (RDWS). It handles most associated complexities including authentication, entity abstraction, errors and more.

## Contents

- [Getting started](#getting-started)
- [Prerequisites](#prerequisites)
- [Creating a client](#creating-a-client)
- [Integrating with Laravel](#integrating-with-laravel)
- [Available methods](#available-methods)
- [Contributing](#contributing)

## Getting started

Install the SDK into your project using Composer.

```bash
composer config repositories.retail-directions-sdk git git@github.com:arkade-digital/retail-directions-sdk.git
composer require arkade/retail-directions-sdk
```

## Prerequisites

To begin sending requests to App Engine, you will need a few pieces of information.

- __Endpoint__ This is the URL for the specific App Engine instance you wish to connect to. It will look something like `https://client.omneo.io/api/`. Note the trailing slash is required.

Depending on your authentication mechanism, you will need some secrets associated with that authentication type. As we currently only support HMAC signature authentication, you will need the following items.

- __HMAC Key__ Internally called `access_key_id`, this is generated as part of your App Engine user profile. Ask your App Engine instance administrator for this information.
- __HMAC Secret__ Internally called `access_key_secret`, this is also generated as part of your App Engine user profile. Ask your App Engine instance administrator for this information.

## Creating a client

> If you are using Laravel, skip to the [Integrating with Laravel](#integrating-with-laravel) section

To begin using the SDK, you will first need to create an authenticated client with the information you have obtained above.

```php
$client = (new Client('https://client.omneo.io/api/'))
    ->setCredentials(new Credentials\HMAC('YOUR_KEY', 'YOUR_SECRET'));
```

If you create a client without setting credentials, all your requests will be sent without appropriate authentication headers and will most likely result in an unauthorised response.

## Integrating with Laravel

This package ships with a Laravel specific service provider which allows you to set your credentials from your configuration file and environment.

### Registering the provider

Add the following to the `providers` array in your `config/app.php` file.

```php
Arkade\AppEngine\LaravelServiceProvider::class
```

### Adding config keys

In your `config/services.php` file, add the following to the array.

```php
'appengine' => [
    'endpoint' => env('APPENGINE_ENDPOINT'),
    'key'      => env('APPENGINE_KEY'),
    'secret'   => env('APPENGINE_SECRET'),
]
```

### Adding environment keys

In your `.env` file, add the following keys.

```ini
APPENGINE_ENDPOINT=
APPENGINE_KEY=
APPENGINE_SECRET=
```

### Resolving a client

To resolve a fully authenticated client, you simply pull it from the service container. This can be done in a few ways.

#### Type hinting

```php
use Arkade\AppEngine\Client;

public function yourControllerMethod(Client $client) {
    // Call methods on $client
}
```

#### Using the `app()` helper

```php
use Arkade\AppEngine\Client;

public function anyMethod() {
    $client = app(Client::class);
    // Call methods on $client
}
```

## Available methods

### Auth module

#### Authenticate user credentials

When provided a valid email address and password combination, this method will return an `Arkade\AppEngine\Entities\User` instance containing the attributes that App Engine holds for this user.

If the provided credentials are incorrect, this method will throw `Arkade\AppEngine\Exceptions\UnauthorizedException` which you should handle in your application.

```php
$client->auth()->authenticateUserCredentials($email, $password);
```

### Users module

#### Create or update (sync) a user

When provided an array of attributes, a matching user will be created or updated within Loyalty Engine. At a minimum, attributes should contain an `email` key which is used to identify a user.

If successful, this method will return a `Arkade\AppEngine\Entities\User` instance containing the attributes that App Engine now holds for this user.

```php
$client->users()->sync($attributes);
```

## Contributing

If you wish to contribute to this library, please submit a pull request and assign to a member of Capcom for review.

All public methods should be accompanied with unit tests.

### Testing

```bash
./vendor/bin/phpunit
```