<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Position;

final class Positions
{
    /**
     * @var array
     */
    private $accepted;

    /**
     * @var array
     */
    private $declined;

    public function __construct(array $accepted, array $declined)
    {
        $this->accepted = $accepted;
        $this->declined = $declined;
    }

    public function getAccepted(): array
    {
        return $this->accepted;
    }

    public function hasAccepted(): bool
    {
        return count($this->accepted) > 0;
    }

    public function getDeclined(): array
    {
        return $this->declined;
    }

    public function hasDeclined(): bool
    {
        return count($this->declined) > 0;
    }

    public function hasPositions(): bool
    {
        return $this->hasAccepted() || $this->hasDeclined();
    }
}
