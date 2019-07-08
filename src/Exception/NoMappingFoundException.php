<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Exception;

use InvalidArgumentException;
use Safe\Exceptions\JsonException;
use Safe\Exceptions\StringsException;
use function Safe\json_encode;
use function Safe\sprintf;

final class NoMappingFoundException extends InvalidArgumentException
{
    /**
     * @throws JsonException
     * @throws StringsException
     */
    public function __construct(array $item)
    {
        $message = sprintf('No mapping found for item %s', json_encode($item));

        parent::__construct($message);
    }
}
