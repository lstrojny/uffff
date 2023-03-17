<?php

declare(strict_types=1);

namespace Uffff\Filter;

use Uffff\Contract\Filter;
use Webmozart\Assert\Assert;

/**
 * @psalm-immutable
 * @internal
 */
readonly final class AssertWellFormedUnicode implements Filter
{
    private const ALL_VALID_CODEPOINTS = '/^.*$/us';

    /**
     * @phpstan-pure
     */
    public function __invoke(string $text): string
    {
        Assert::notFalse(
            preg_match(self::ALL_VALID_CODEPOINTS, $text),
            sprintf('Value "%s" (%s) contains non-unicode characters', $text, bin2hex($text))
        );

        return $text;
    }
}
