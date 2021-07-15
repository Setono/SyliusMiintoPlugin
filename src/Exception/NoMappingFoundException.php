<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Exception;

use InvalidArgumentException;
use function json_encode;
use function sprintf;

final class NoMappingFoundException extends InvalidArgumentException
{
    public function __construct(array $item)
    {
        $message = sprintf('No mapping found for item %s', json_encode($item));

        parent::__construct($message);
    }
}
