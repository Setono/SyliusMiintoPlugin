<?php

declare(strict_types=1);

namespace Setono\SyliusMiintoPlugin\Exception;

use InvalidArgumentException;
use function sprintf;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ConstraintViolationException extends InvalidArgumentException
{
    /** @var ConstraintViolationListInterface */
    private $constraintViolationList;

    public function __construct(ConstraintViolationListInterface $constraintViolationList)
    {
        $this->constraintViolationList = $constraintViolationList;

        $string = '';
        foreach ($this->constraintViolationList as $violation) {
            $string .= $violation . "\n";
        }

        parent::__construct(sprintf('Validation error(s): %s', $string));
    }

    public function getConstraintViolationList(): ConstraintViolationListInterface
    {
        return $this->constraintViolationList;
    }
}
