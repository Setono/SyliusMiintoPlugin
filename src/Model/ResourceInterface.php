<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

use Sylius\Component\Resource\Model\ResourceInterface as BaseResourceInterface;

interface ResourceInterface extends BaseResourceInterface
{
    /**
     * @param string|int $id
     */
    public function setId($id): void;

    /**
     * @param array $data
     */
    public function setData(array $data): void;

    /**
     * @return array
     */
    public function getData(): array;
}
