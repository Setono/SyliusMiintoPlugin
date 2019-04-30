<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Form\Type;

use Setono\SyliusMiintoPlugin\Model\ShippingMethodMappingInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class ShippingTypeMappingType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', TextType::class, [
                'label' => 'setono_sylius_miinto.form.shipping_type_mapping.type',
                'disabled' => true,
            ])
            ->add('shop', TextType::class, [
                'label' => 'setono_sylius_miinto.form.shipping_type_mapping.shop',
                'disabled' => true,
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, static function (FormEvent $event) {
            /** @var ShippingMethodMappingInterface $mapping */
            $mapping = $event->getData();

            $shop = $mapping->getShop();
            if (null === $shop) {
                return;
            }

            $channel = $shop->getChannel();
            if (null !== $channel) {
                $form = $event->getForm();

                $form->add('shippingMethod', ShippingMethodChoiceType::class, [
                    'label' => 'setono_sylius_miinto.form.shipping_type_mapping.shipping_method',
                    'placeholder' => 'setono_sylius_miinto.form.shipping_type_mapping.choose_shipping_method',
                    'channel' => $channel,
                ]);
            }
        });
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_miinto_shipping_type_mapping';
    }
}
