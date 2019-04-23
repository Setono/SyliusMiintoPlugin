<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\DependencyInjection;

use Setono\SyliusMiintoPlugin\Doctrine\ORM\MappingRepository;
use Setono\SyliusMiintoPlugin\Doctrine\ORM\OrderRepository;
use Setono\SyliusMiintoPlugin\Form\Type\MappingType;
use Setono\SyliusMiintoPlugin\Form\Type\ShopType;
use Setono\SyliusMiintoPlugin\Model\Error;
use Setono\SyliusMiintoPlugin\Model\ErrorInterface;
use Setono\SyliusMiintoPlugin\Model\Mapping;
use Setono\SyliusMiintoPlugin\Model\MappingInterface;
use Setono\SyliusMiintoPlugin\Model\Order;
use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Setono\SyliusMiintoPlugin\Model\Shop;
use Setono\SyliusMiintoPlugin\Model\ShopInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\Form\Type\DefaultResourceType;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        if (method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder('setono_sylius_miinto');
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('setono_sylius_miinto');
        }

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('http_client')
                    ->cannotBeEmpty()
                    ->isRequired()
                    ->info('The service id for your PSR18 HTTP client')
                ->end()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->cannotBeEmpty()->end()
                ->scalarNode('product_variant_gtin_field')
                    ->cannotBeEmpty()
                    ->defaultValue('gtin')
                    ->example('gtin')
                    ->info('The field on your product variant resource that contains the GTIN')
                ->end()
                ->arrayNode('messenger')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('transport')
                            ->cannotBeEmpty()
                            ->defaultNull()
                            ->example('amqp')
                            ->info('The Messenger transport to use')
                        ->end()
                        ->scalarNode('command_bus')
                            ->cannotBeEmpty()
                            ->defaultValue('message_bus')
                            ->example('message_bus')
                            ->info('The service id for your command bus')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('miinto')
                    ->children()
                        ->scalarNode('auth_endpoint')
                            ->defaultValue('https://api-auth.miinto.net')
                            ->info('This is the endpoint to auth with Miinto')
                            ->example('https://api-auth.miinto.net')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('resource_endpoint')
                            ->defaultValue('https://api-order.miinto.net')
                            ->info('This is the endpoint for Miintos Order API')
                            ->example('https://api-order.miinto.net')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('username')->cannotBeEmpty()->isRequired()->end()
                        ->scalarNode('password')->cannotBeEmpty()->isRequired()->end()
                    ->end()
                ->end()
            ->end()
        ;

        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    private function addResourcesSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('shop')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('model')->defaultValue(Shop::class)->cannotBeEmpty()->end()
                                            ->scalarNode('interface')->defaultValue(ShopInterface::class)->cannotBeEmpty()->end()
                                            ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                            ->scalarNode('repository')->cannotBeEmpty()->end()
                                            ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                            ->scalarNode('form')->defaultValue(ShopType::class)->cannotBeEmpty()->end()
                                        ->end()
                                    ->end()
                                ->end()
                        ->end()
                        ->arrayNode('order')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Order::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(OrderInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(OrderRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(DefaultResourceType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('error')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Error::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ErrorInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(DefaultResourceType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('mapping')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Mapping::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(MappingInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(MappingRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(MappingType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
