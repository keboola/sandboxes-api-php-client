# Sandboxes API PHP client by Keboola
[![Latest Stable Version](https://poser.pugx.org/keboola/sandboxes-api-php-client/v/stable.svg)](https://packagist.org/packages/keboola/sandboxes-api-php-client) [![License](https://poser.pugx.org/keboola/sandboxes-api-php-client/license.svg)](https://packagist.org/packages/keboola/sandboxes-api-php-client) [![Total Downloads](https://poser.pugx.org/keboola/sandboxes-api-php-client/downloads.svg)](https://packagist.org/packages/keboola/sandboxes-api-php-client)

## Status

[![Build Status](https://travis-ci.org/keboola/sandboxes-api-php-client.svg)](https://travis-ci.org/keboola/sandboxes-api-php-client)


## Installation

Library is available as composer package.
To start using composer in your project follow these steps:

**Install composer**
  
```bash
curl -s http://getcomposer.org/installer | php
mv ./composer.phar ~/bin/composer # or /usr/local/bin/composer
```

**Create composer.json file in your project root folder:**
```json
{
    "require": {
        "keboola/sandboxes-api-php-client": "~1.0"
    }
}
```

**Install package:**

```bash
composer install
```

**Add autoloader in your bootstrap script:**

```php
require 'vendor/autoload.php';
```

Read more in [Composer documentation](http://getcomposer.org/doc/01-basic-usage.md)

## Usage examples

```php
require 'vendor/autoload.php';

$client = new \Keboola\Sandboxes\Api\Client(API_URL);


```
