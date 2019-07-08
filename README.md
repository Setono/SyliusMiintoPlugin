# Sylius Miinto Plugin

[![Latest Stable Version][ico-version]][link-packagist]
[![Latest Unstable Version][ico-unstable-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-code-quality]][link-code-quality]

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
in `config/bundles.php` file of your project before (!) `SyliusGridBundle`:

```php
<?php

# config/bundles.php

return [
    // ...
    Setono\SyliusMiintoPlugin\SetonoSyliusMiintoPlugin::class => ['all' => true],
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
    miinto:
        username: '%env(MIINTO_USERNAME)%'
        password: '%env(MIINTO_PASSWORD)%'
```

### Step 6: Using asynchronous transport (optional, but recommended)
```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        routing:
            # Route all command messages to the async transport
            # This presumes that you have already set up an 'async' transport
            'Setono\SyliusMiintoPlugin\Message\Command\CommandInterface': async
```

[ico-version]: https://poser.pugx.org/setono/sylius-miinto-plugin/v/stable
[ico-unstable-version]: https://poser.pugx.org/setono/sylius-miinto-plugin/v/unstable
[ico-license]: https://poser.pugx.org/setono/sylius-miinto-plugin/license
[ico-travis]: https://travis-ci.com/Setono/SyliusMiintoPlugin.svg?branch=master
[ico-code-quality]: https://scrutinizer-ci.com/g/Setono/SyliusMiintoPlugin/badges/quality-score.png?b=master

[link-packagist]: https://packagist.org/packages/setono/sylius-miinto-plugin
[link-travis]: https://travis-ci.com/Setono/SyliusMiintoPlugin
[link-code-quality]: https://scrutinizer-ci.com/g/Setono/SyliusMiintoPlugin
