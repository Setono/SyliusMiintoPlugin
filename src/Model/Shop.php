<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Model;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

class Shop implements ShopInterface
{
    /** @var string|null */
    protected $id;

    /** @var string|null */
    protected $name;

    /** @var ChannelInterface|null */
    protected $channel;

    /** @var LocaleInterface|null */
    protected $locale;

    public function __toString(): string
    {
        return $this->name . ' (id: ' . $this->id . ')';
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
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

    public function getLocale(): ?LocaleInterface
    {
        return $this->locale;
    }

    public function setLocale(?LocaleInterface $locale): void
    {
        $this->locale = $locale;
    }
}
