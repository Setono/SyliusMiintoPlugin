<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Exception;

use Safe\Exceptions\StringsException;

final class AuthenticationFailedException extends \RuntimeException
{
    /**
     * @param string $username
     * @param string $password
     * @param string $status
     *
     * @throws StringsException
     */
    public function __construct(string $username, string $password, string $status = '')
    {
        $message = \Safe\sprintf('Authentication failed with username: %s and password: %s.', $username, str_repeat('*', strlen($password)));
        if ('' !== $status) {
            $message .= ' Status was ' . $status;
        }

        parent::__construct($message);
    }
}
