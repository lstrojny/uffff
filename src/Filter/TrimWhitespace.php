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
    /**
     * @phpstan-pure
     */
    public function __invoke(string $text): string
    {
        $characters = '(?![' . BidirectionalMarker::characters() . "])[\p{Zs}\p{Cc}]+";

        $trimmed = preg_replace('/^' . $characters . '|' . $characters . '$/u', '', $text);

        Assert::string($trimmed, sprintf('Value "%s" cannot be trimmed', $text));

        return $trimmed;
    }
}
