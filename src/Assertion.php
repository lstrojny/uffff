<?php

declare(strict_types=1);

namespace Uffff;

use Assert\Assertion as BaseAssertion;
use Assert\AssertionFailedException;

/**
 * @psalm-immutable
 */
final class Assertion extends BaseAssertion
{
    /**
     * @param mixed $value
     * @param string|callable|null $message
     * @return bool
     * @throws AssertionFailedException
     * @psalm-pure
     */
    public static function string($value, $message = null, string $propertyPath = null)
    {
        /** @psalm-suppress ImpureMethodCall */
        return parent::string($value, $message, $propertyPath);
    }

    /**
     * @param mixed $value1
     * @param mixed $value2
     * @param string|callable|null $message
     * @throws AssertionFailedException
     * @psalm-pure
     */
    public static function notEq($value1, $value2, $message = null, string $propertyPath = null): bool
    {
        /** @psalm-suppress ImpureMethodCall */
        return parent::notEq($value1, $value2, $message, $propertyPath);
    }
}
