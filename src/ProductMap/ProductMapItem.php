<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\ProductMap;

final class ProductMapItem
{
    /**
     * @var string
     */
    private $itemGroupIdOrStyleId;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $gtin;

    /**
     * @var string
     */
    private $miintoItemId;

    public function __construct(string $itemGroupIdOrStyleId, string $id, string $gtin, string $miintoItemId)
    {
        $this->itemGroupIdOrStyleId = $itemGroupIdOrStyleId;
        $this->id = $id;
        $this->gtin = $gtin;
        $this->miintoItemId = $miintoItemId;
    }

    public function getItemGroupIdOrStyleId(): string
    {
        return $this->itemGroupIdOrStyleId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getGtin(): string
    {
        return $this->gtin;
    }

    public function getMiintoItemId(): string
    {
        return $this->miintoItemId;
    }
}
