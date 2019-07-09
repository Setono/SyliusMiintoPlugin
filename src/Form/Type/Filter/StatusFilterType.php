<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Form\Type\Filter;

use Safe\Exceptions\ArrayException;
use Setono\SyliusMiintoPlugin\Model\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class StatusFilterType extends AbstractType
{
    /**
     * @throws ArrayException
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('status', ChoiceType::class, [
            'placeholder' => 'setono_sylius_miinto.form.status.all',
            'label' => false,
            'choices' => $this->getStatuses(),
        ]);
    }

    /**
     * @throws ArrayException
     */
    private function getStatuses(): array
    {
        $statuses = Order::getStatuses();

        array_walk($statuses, static function (&$elm) {
            $elm = 'setono_sylius_miinto.form.status.' . $elm;
        });

        return \Safe\array_flip($statuses);
    }
}
