<?php

declare(strict_types=1);

namespace Uffff\Filter;

use Uffff\Contracts\Filter;
use Webmozart\Assert\Assert;

/**
 * @psalm-immutable
 * @internal
 */
readonly final class CheckIfUnicode implements Filter
{
    public function __invoke(string $value): string
    {
        Assert::notFalse(preg_match('/^.*$/us', $value), 'Value "%s" contains non-unicode characters');

        return $value;
    }
}
