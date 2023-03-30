<?php

declare(strict_types=1);

namespace Uffff\Filter;

use Uffff\Contract\Filter;
use Uffff\Value\BidirectionalMarker;
use Webmozart\Assert\Assert;

/**
 * @psalm-immutable
 * @internal
 */
final readonly class TrimWhitespace implements Filter
{
    private const WHITESPACE = '(?![' . BidirectionalMarker::CHARACTERS . '])[\p{Zs}\p{Cc}]+';

    private const ANY_LEADING_OR_TRAILING_WHITESPACE = '/^' . self::WHITESPACE . '|' . self::WHITESPACE . '$/u';

    /**
     * @phpstan-pure
     */
    public function __invoke(string $text): string
    {
        $trimmed = preg_replace(self::ANY_LEADING_OR_TRAILING_WHITESPACE, '', $text);

        Assert::string($trimmed, sprintf('Value "%s" cannot be trimmed', $text));

        return $trimmed;
    }
}
