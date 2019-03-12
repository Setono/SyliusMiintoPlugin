<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

abstract class AbstractResource implements ResourceInterface
{
    /**
     * This is the id in Miinto if there is one else it should be auto generated
     *
     * @var int
     */
    protected $id;

    /**
     * @var array
     */
    protected $data = [];

    // @todo should we have a constructor with id and data in it?

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
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
