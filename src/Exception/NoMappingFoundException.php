<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Exception;

use InvalidArgumentException;

final class NoMappingFoundException extends InvalidArgumentException
{
    public function __construct(array $item)
    {
        $message = \Safe\sprintf('No mapping found for item %s', \Safe\json_encode($item));

        parent::__construct($message);
    }
}
