<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Form\Type;

use Setono\SyliusMiintoPlugin\Model\MappingInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class MappingType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('providerId', TextType::class, [
                'label' => 'setono_sylius_miinto.form.mapping.provider_id',
                'disabled' => true,
            ])
            ->add('shop', TextType::class, [
                'label' => 'setono_sylius_miinto.form.mapping.shop',
                'disabled' => true,
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            /** @var MappingInterface $mapping */
            $mapping = $event->getData();

            $shop = $mapping->getShop();
            if (null === $shop) {
                return;
            }

            $channel = $shop->getChannel();
            if (null !== $channel) {
                $form = $event->getForm();

                $form->add('shippingMethod', ShippingMethodChoiceType::class, [
                    'label' => 'setono_sylius_miinto.form.mapping.shipping_method',
                    'placeholder' => 'setono_sylius_miinto.form.mapping.choose_shipping_method',
                    'channel' => $channel,
                ]);

                $form->add('paymentMethod', PaymentMethodChoiceType::class, [
                    'label' => 'setono_sylius_miinto.form.mapping.payment_method',
                    'placeholder' => 'setono_sylius_miinto.form.mapping.choose_payment_method',
                    'channel' => $channel,
                ]);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'setono_sylius_miinto_mapping';
    }
}
