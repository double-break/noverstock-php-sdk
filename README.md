## Installation

`composer require double-break/noverstock-php-sdk`

## Usage

### Orders Client
```php
<?php
use DoubleBreak\Noverstock\Sdk\ClientFactory;

require __DIR__ . '/../vendor/autoload.php';

$factory = new ClientFactory([
    'actor' => [
        'type' => 'APP',
        'authToken' => 'eyJ0eX...ug2vk'
    ]
]);
$client = $factory->createClient('Orders');

$result = $client->getOrder('order-uuid');
print_r($result['order']);
```


### Generic Client

```php

use DoubleBreak\Noverstock\Sdk\ClientFactory;

require __DIR__ . '/../vendor/autoload.php';


$factory = new ClientFactory([
    'actor' => [
        'type' => 'APP',
        'authToken' => 'eyJ0eX...ug2vk'
    ]
]);


$client = $factory->createClient('Generic');
$result = $client->request('GET', '/orders-api/orders/{uuid}', [
    'path' => [
       'uuid' => 'order-uuid'
    ]
]);
print_r($result['order']);

```


### Connecting to Test Environment

```php
use DoubleBreak\Noverstock\Sdk\ClientFactory;

require __DIR__ . '/../vendor/autoload.php';

$factory = new ClientFactory([
    'domain' = 'test.noverstock.tech'
    'actor' => [
        'type' => 'APP',
        'authToken' => 'eyJ0eX...ug2vk'
    ]
]);
$client = $factory->createClient('Orders');

$result = $client->getOrder('order-uuid');
print_r($result['order']);
```