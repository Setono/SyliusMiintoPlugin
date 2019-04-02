<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Form\Type\Filter;

use Setono\SyliusMiintoPlugin\Model\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class StatusFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('status', ChoiceType::class, [
            'placeholder' => 'setono_sylius_miinto.form.status.all',
            'label' => false,
            'choices' => $this->getStatuses(),
        ]);
    }

    private function getStatuses(): array
    {
        $statuses = Order::getStatuses();

        array_walk($statuses, function (&$elm) {
            $elm = 'setono_sylius_miinto.form.status.' . $elm;
        });

        return array_flip($statuses);
    }
}
