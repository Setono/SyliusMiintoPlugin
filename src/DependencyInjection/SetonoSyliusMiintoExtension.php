<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SetonoSyliusMiintoExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->setParameter('setono_sylius_miinto.product_variant_gtin_field', $config['product_variant_gtin_field']);
        $container->setParameter('setono_sylius_miinto.miinto.auth_endpoint', $config['miinto']['auth_endpoint']);
        $container->setParameter('setono_sylius_miinto.miinto.resource_endpoint', $config['miinto']['resource_endpoint']);
        $container->setParameter('setono_sylius_miinto.miinto.username', $config['miinto']['username']);
        $container->setParameter('setono_sylius_miinto.miinto.password', $config['miinto']['password']);

        $loader->load('services.xml');

        $this->registerResources('setono_sylius_miinto', $config['driver'], $config['resources'], $container);
    }
}
