<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

use Sylius\Component\Channel\Model\ChannelInterface;

class Shop extends AbstractResource implements ShopInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var ChannelInterface
     */
    protected $channel;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(?ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }
}
