# Sylius Miinto Plugin

[![Latest Version][ico-version]][link-packagist]
[![Latest Unstable Version][ico-unstable-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Code Coverage][ico-code-coverage]][link-code-coverage]

Implements Miintos [Order API](http://www.integrations.miinto.net/order-api) into your Sylius store.

## Installation

### Step 1: Download the plugin

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```bash
$ composer require setono/sylius-miinto-plugin
```

This command requires you to have Composer installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.


### Step 2: Enable the plugin

Then, enable the plugin by adding it to the list of registered plugins/bundles
in `config/bundles.php` file of your project before (!) `SyliusGridBundle` and `FrameworkBundle`:

```php
<?php

# config/bundles.php

return [
    Setono\SyliusMiintoPlugin\SetonoSyliusMiintoPlugin::class => ['all' => true],
    // ...
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Sylius\Bundle\GridBundle\SyliusGridBundle::class => ['all' => true],
    // ...
];
```

### Step 3: Configure the plugin

```yaml
# config/packages/_sylius.yaml

imports:
    # ...
    
    - { resource: "@SetonoSyliusMiintoPlugin/Resources/config/app/config.yaml" }
    
    # ...

```

```yaml
# config/routes/setono_sylius_miinto.yaml

setono_sylius_miinto:
    resource: "@SetonoSyliusMiintoPlugin/Resources/config/routing.yaml"
```

### Step 4: Update your database schema

```bash
$ php bin/console doctrine:migrations:diff
$ php bin/console doctrine:migrations:migrate
```

### Step 5: Input required configuration options
```yaml
# config/packages/setono_sylius_miinto.yaml

setono_sylius_miinto:
    product_variant_gtin_field: gtin
    miinto:
        username: '%env(MIINTO_USERNAME)%'
        password: '%env(MIINTO_PASSWORD)%'
```

The `product_variant_gtin_field` configuration option is important, since this is the default way to match products
from Miinto with products in your store. It is used by the [ProductVariantMapper](src/Mapper/ProductVariantMapper.php).
If you haven't added a GTIN field to your variants, you can use the [Barcode plugin](https://github.com/loevgaard/SyliusBarcodePlugin).

If you don't use GTIN's you should override the `ProductVariantMapper` service (`setono_sylius_miinto.mapper.product_variant`) with your own implementation.

#### Sandbox configuration

When you want to use sandbox credentials, you should specify special endpoint URLs as well:

```yaml
# config/packages/dev/setono_sylius_miinto.yaml

setono_sylius_miinto:
    miinto:
        auth_endpoint: https://api-auth-sandbox.miinto.net
        resource_endpoint: https://api-order-sandbox.miinto.net
```

### Step 6: Using asynchronous transport (optional, but recommended)

All commands in this plugin will extend the [CommandInterface](src/Message/Command/CommandInterface.php).
Therefore you can route all commands easily by adding this to your [Messenger config](https://symfony.com/doc/current/messenger.html#routing-messages-to-a-transport):

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        routing:
            # Route all command messages to the async transport
            # This presumes that you have already set up an 'async' transport
            'Setono\SyliusMiintoPlugin\Message\Command\CommandInterface': async
```

## Usage

The plugin works in a two phase manner: First it handles pending transfers telling Miinto which transfers
to accept and which to decline. The next phase takes the accepted positions (orders) and converts these orders
into Sylius orders.

The first command (phase one), which you should run every minute, is this one:

```bash
$ php bin/console setono:sylius-miinto:pending-transfers
```

The next one (phase two) will handle the orders. This command doesn't have to run as often. Every 5 or 10 minutes should be sufficient:

```bash
$ php bin/console setono:sylius-miinto:process-orders
```

## Troubleshooting

- `You have requested a non-existent parameter "setono_sylius_miinto.model.order.class".`
  
  You defined plugin after `SyliusGridBundle` or `FrameworkBundle`.

[ico-version]: https://poser.pugx.org/setono/sylius-miinto-plugin/v/stable
[ico-unstable-version]: https://poser.pugx.org/setono/sylius-miinto-plugin/v/unstable
[ico-license]: https://poser.pugx.org/setono/sylius-miinto-plugin/license
[ico-github-actions]: https://github.com/Setono/SyliusMiintoPlugin/workflows/build/badge.svg
[ico-code-coverage]: https://codecov.io/gh/Setono/SyliusMiintoPlugin/branch/master/graph/badge.svg

[link-packagist]: https://packagist.org/packages/setono/sylius-miinto-plugin
[link-github-actions]: https://github.com/Setono/SyliusMiintoPlugin/actions
[link-code-coverage]: https://codecov.io/gh/Setono/SyliusMiintoPlugin
