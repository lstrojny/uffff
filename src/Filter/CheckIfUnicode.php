<?php

declare(strict_types=1);

namespace Uffff\Filter;

use Uffff\Contract\Filter;
use Webmozart\Assert\Assert;

/**
 * @psalm-immutable
 * @internal
 */
readonly final class CheckIfUnicode implements Filter
{
    /**
     * @phpstan-pure
     */
    public function __invoke(string $text): string
    {
        Assert::notFalse(
            preg_match('/^.*$/us', $text),
            sprintf('Value "%s" (%s) contains non-unicode characters', $text, bin2hex($text))
        );

        return $text;
    }
}
