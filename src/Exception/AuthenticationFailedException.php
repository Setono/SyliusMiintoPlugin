<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Exception;

use RuntimeException;
use function sprintf;

final class AuthenticationFailedException extends RuntimeException
{
    public function __construct(string $username, string $password, string $status = '')
    {
        $message = sprintf('Authentication failed with username: %s and password: %s.', $username, str_repeat('*', strlen($password)));
        if ('' !== $status) {
            $message .= ' Status was ' . $status;
        }

        parent::__construct($message);
    }
}
