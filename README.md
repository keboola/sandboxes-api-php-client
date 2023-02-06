# Sandboxes API PHP client by Keboola
[![Latest Stable Version](https://poser.pugx.org/keboola/sandboxes-api-php-client/v/stable.svg)](https://packagist.org/packages/keboola/sandboxes-api-php-client) [![License](https://poser.pugx.org/keboola/sandboxes-api-php-client/license.svg)](https://packagist.org/packages/keboola/sandboxes-api-php-client) [![Total Downloads](https://poser.pugx.org/keboola/sandboxes-api-php-client/downloads.svg)](https://packagist.org/packages/keboola/sandboxes-api-php-client) ![Build](https://github.com/keboola/sandboxes-api-php-client/workflows/Build/badge.svg)

<https://sandboxes.keboola.com/documentation>

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


## CI

CI is running in GitHub and requires setup of these secrets:
- `ACR_PULL_USERNAME` - docker credentials for Azure service principal allowed pulling `keboola.azurecr.io/sandboxes-api` (see <https://github.com/keboola/sandboxes#acr-access-from-travis>)
- `ACR_PULL_PASSWORD`
- `KBC_STORAGE_TOKEN` - a Storage token working for connection.keboola.com stack
- `KBC_MANAGE_TOKEN` - Manage token with `provisioning:manage` scope 

## License

MIT licensed, see [LICENSE](./LICENSE) file.
