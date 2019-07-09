<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\DependencyInjection;

use Setono\SyliusMiintoPlugin\Doctrine\ORM\OrderRepository;
use Setono\SyliusMiintoPlugin\Doctrine\ORM\PaymentMethodMappingRepository;
use Setono\SyliusMiintoPlugin\Doctrine\ORM\ShippingMethodMappingRepository;
use Setono\SyliusMiintoPlugin\Doctrine\ORM\ShippingTypeMappingRepository;
use Setono\SyliusMiintoPlugin\Form\Type\PaymentMethodMappingType;
use Setono\SyliusMiintoPlugin\Form\Type\ShippingMethodMappingType;
use Setono\SyliusMiintoPlugin\Form\Type\ShippingTypeMappingType;
use Setono\SyliusMiintoPlugin\Form\Type\ShopType;
use Setono\SyliusMiintoPlugin\Model\Error;
use Setono\SyliusMiintoPlugin\Model\ErrorInterface;
use Setono\SyliusMiintoPlugin\Model\Order;
use Setono\SyliusMiintoPlugin\Model\OrderInterface;
use Setono\SyliusMiintoPlugin\Model\PaymentMethodMapping;
use Setono\SyliusMiintoPlugin\Model\PaymentMethodMappingInterface;
use Setono\SyliusMiintoPlugin\Model\ShippingMethodMapping;
use Setono\SyliusMiintoPlugin\Model\ShippingMethodMappingInterface;
use Setono\SyliusMiintoPlugin\Model\ShippingTypeMapping;
use Setono\SyliusMiintoPlugin\Model\ShippingTypeMappingInterface;
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
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->cannotBeEmpty()->end()
                ->scalarNode('product_variant_gtin_field')
                    ->cannotBeEmpty()
                    ->isRequired()
                    ->example('gtin')
                    ->info('The field on your product variant resource that contains the GTIN')
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
                        ->arrayNode('shipping_method_mapping')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ShippingMethodMapping::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ShippingMethodMappingInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(ShippingMethodMappingRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(ShippingMethodMappingType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('shipping_type_mapping')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ShippingTypeMapping::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ShippingTypeMappingInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(ShippingTypeMappingRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(ShippingTypeMappingType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('payment_method_mapping')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(PaymentMethodMapping::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(PaymentMethodMappingInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(PaymentMethodMappingRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(PaymentMethodMappingType::class)->cannotBeEmpty()->end()
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
