<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\DependencyInjection\Compiler;

use Setono\SyliusMiintoPlugin\Client\Client;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterClientPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $parameters = [
            'setono_sylius_miinto.http_client',
            'setono_sylius_miinto.miinto.auth_endpoint',
            'setono_sylius_miinto.miinto.resource_endpoint',
            'setono_sylius_miinto.miinto.username',
            'setono_sylius_miinto.miinto.password',
        ];

        foreach ($parameters as $parameter) {
            if (!$container->hasParameter($parameter)) {
                return;
            }
        }

        $services = [
            $container->getParameter('setono_sylius_miinto.http_client'),
            'setono_sylius_miinto.http_client.request_factory',
            'setono_sylius_miinto.http_client.stream_factory',
        ];

        foreach ($services as $service) {
            if (!$container->has($service)) {
                return;
            }
        }

        $definition = new Definition(Client::class, [
            new Reference($container->getParameter('setono_sylius_miinto.http_client')),
            new Reference('setono_sylius_miinto.http_client.request_factory'),
            new Reference('setono_sylius_miinto.http_client.stream_factory'),
            $container->getParameter('setono_sylius_miinto.miinto.auth_endpoint'),
            $container->getParameter('setono_sylius_miinto.miinto.resource_endpoint'),
            $container->getParameter('setono_sylius_miinto.miinto.username'),
            $container->getParameter('setono_sylius_miinto.miinto.password'),
        ]);

        $container->setDefinition('setono_sylius_miinto.client.default', $definition);
    }
}
