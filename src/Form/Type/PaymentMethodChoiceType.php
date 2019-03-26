<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Form\Type;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PaymentMethodChoiceType extends AbstractType
{
    /**
     * @var PaymentMethodRepositoryInterface
     */
    private $repository;

    public function __construct(PaymentMethodRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    return $this->repository->findEnabledForChannel($options['channel']);
                },
                'choice_value' => 'code',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
            ])
            ->setRequired([
                'channel',
            ])
            ->setAllowedTypes('channel', ChannelInterface::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'setono_sylius_miinto_payment_method_choice';
    }
}
