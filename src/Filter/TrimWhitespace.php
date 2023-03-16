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
readonly final class TrimWhitespace implements Filter
{
    private const WHITESPACE = '(?![' . BidirectionalMarker::CHARACTERS . '])[\p{Zs}\p{Cc}]+';

    private const REGEX = '/^' . self::WHITESPACE . '|' . self::WHITESPACE . '$/u';

    /**
     * @phpstan-pure
     */
    public function __invoke(string $text): string
    {
        $trimmed = preg_replace(self::REGEX, '', $text);

        Assert::string($trimmed, sprintf('Value "%s" cannot be trimmed', $text));

        return $trimmed;
    }
}
