<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class DumpExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('setono_sylius_miinto_dump', [$this, 'dump'], ['is_safe' => ['html']]),
        ];
    }

    public function dump(array $val): string
    {
        return '<pre>' . print_r($val, true) . '</pre>';
    }
}
