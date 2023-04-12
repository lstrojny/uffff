<?php

declare(strict_types=1);

namespace Uffff\Filter;

use Uffff\Contract\Filter;
use Uffff\Value\ValueContext;
use Webmozart\Assert\Assert;

/**
 * @psalm-immutable
 * @internal
 */
final readonly class AssertWellFormedUnicode implements Filter
{
    private const ALL_VALID_CODEPOINTS = '/^.*$/us';

    public function __construct(
        private ValueContext $context
    ) {
    }

    public function __invoke(string $text): string
    {
        Assert::notFalse(
            preg_match(self::ALL_VALID_CODEPOINTS, $text),
            sprintf('%s value "%s" (%s) contains non-unicode characters', match ($this->context) {
                ValueContext::INPUT => 'Input',
                ValueContext::OUTPUT => 'Output',
            }, $text, bin2hex($text))
        );

        return $text;
    }
}
