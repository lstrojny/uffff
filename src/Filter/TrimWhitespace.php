<?php

declare(strict_types=1);

namespace Uffff\Filter;

use Uffff\Contracts\Filter;
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
    public function __invoke(string $value): string
    {
        $characters = '(?![' . BidirectionalMarker::characters() . "])[\p{Zs}\p{Cc}]+";

        $trimmed = preg_replace('/^' . $characters . '|' . $characters . '$/u', '', $value);

        Assert::string($trimmed, sprintf('Value "%s" cannot be trimmed', $value));

        return $trimmed;
    }
}
