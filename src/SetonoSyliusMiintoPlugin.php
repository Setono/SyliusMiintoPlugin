<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

final class SetonoSyliusMiintoPlugin extends AbstractResourceBundle
{
    use SyliusPluginTrait;

    public function getSupportedDrivers(): array
    {
        return [SyliusResourceBundle::DRIVER_DOCTRINE_ORM];
    }
}
