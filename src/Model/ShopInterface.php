<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface as BaseResourceInterface;

interface ShopInterface extends BaseResourceInterface, ChannelAwareInterface
{
    /**
     * @return string
     */
    public function __toString(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     */
    public function setName(string $name): void;
}
