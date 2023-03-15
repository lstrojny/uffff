<?php

declare(strict_types=1);

namespace Uffff\Filter;

use Assert\AssertionFailedException;
use Uffff\Assertion;
use Uffff\Contracts\Filter;

/**
 * @psalm-immutable
 */
readonly final class CheckIfUnicode implements Filter
{
    /**
     * @throws AssertionFailedException
     */
    public function __invoke(string $value): string
    {
        Assertion::notEq(
            false,
            preg_match('/^.*$/us', $value),
            sprintf('Value "%s" contains non-unicode characters', $value)
        );

        return $value;
    }
}
