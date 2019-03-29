<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

interface MutableIdInterface
{
    /**
     * @param int|string $id
     */
    public function setId($id): void;
}
