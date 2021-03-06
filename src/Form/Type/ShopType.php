<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\LocaleBundle\Form\Type\LocaleChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ShopType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'setono_sylius_miinto.form.shop.name',
                'disabled' => true,
            ])
            ->add('channel', ChannelChoiceType::class, [
                'label' => 'setono_sylius_miinto.form.shop.channel',
                'placeholder' => 'setono_sylius_miinto.form.shop.choose_channel',
            ])
            // todo only show locales enabled in the above channel
            ->add('locale', LocaleChoiceType::class, [
                'label' => 'setono_sylius_miinto.form.shop.locale',
                'placeholder' => 'setono_sylius_miinto.form.shop.choose_locale',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'setono_sylius_miinto_shop';
    }
}
