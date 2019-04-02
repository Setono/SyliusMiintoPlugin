<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Grid\Filter;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

class StatusFilter implements FilterInterface
{
    public function apply(DataSourceInterface $dataSource, $name, $data, array $options = []): void
    {
        $dataSource->restrict($dataSource->getExpressionBuilder()->equals('status', $data['status']));
    }
}
