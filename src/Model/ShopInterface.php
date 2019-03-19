<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

use Sylius\Component\Channel\Model\ChannelAwareInterface;

interface ShopInterface extends ResourceInterface, ChannelAwareInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     */
    public function setName(string $name): void;
}
