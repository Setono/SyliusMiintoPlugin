<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

abstract class AbstractResource implements ResourceInterface
{
    /**
     * This is the id in Miinto if there is one, else it should be auto generated
     *
     * @var int|string
     */
    protected $id;

    /**
     * @var array
     */
    protected $data = [];

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
