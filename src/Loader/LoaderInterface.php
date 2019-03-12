<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Loader;

interface LoaderInterface
{
    /**
     * This method will load the data
     */
    public function load(): void;
}
