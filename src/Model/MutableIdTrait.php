<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

trait MutableIdTrait
{
    protected $id;

    public function setId($id): void
    {
        $this->id = $id;
    }
}
