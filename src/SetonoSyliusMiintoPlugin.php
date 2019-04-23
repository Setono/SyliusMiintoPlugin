<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin;

use Setono\SyliusMiintoPlugin\DependencyInjection\Compiler\RegisterClientPass;
use Setono\SyliusMiintoPlugin\DependencyInjection\Compiler\RegisterCommandBusPass;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SetonoSyliusMiintoPlugin extends AbstractResourceBundle
{
    use SyliusPluginTrait;

    public function getSupportedDrivers(): array
    {
        return [SyliusResourceBundle::DRIVER_DOCTRINE_ORM];
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterClientPass());
        $container->addCompilerPass(new RegisterCommandBusPass());
    }
}
