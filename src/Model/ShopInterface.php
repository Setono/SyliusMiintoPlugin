<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface as BaseResourceInterface;

interface ShopInterface extends BaseResourceInterface, ChannelAwareInterface
{
    public function __toString(): string;

    public function getId(): ?string;

    public function setId(string $id): void;

    public function getName(): ?string;

    public function setName(string $name): void;

    public function getLocaleCode(): ?string;

    public function setLocaleCode(string $localeCode): void;
}
