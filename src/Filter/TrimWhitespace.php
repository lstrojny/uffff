<?php

declare(strict_types=1);

namespace Uffff\Filter;

use Assert\AssertionFailedException;
use Uffff\Assertion;
use Uffff\Contracts\Filter;
use Uffff\Value\BidirectionalMarker;

/**
 * @psalm-immutable
 */
readonly final class TrimWhitespace implements Filter
{
    /**
     * @throws AssertionFailedException
     */
    public function __invoke(string $value): string
    {
        $characters = '(?![' . implode('', BidirectionalMarker::values()) . "])[\p{Zs}\p{Cc}\p{Cf}]+";

        $trimmed = preg_replace('/^' . $characters . '|' . $characters . '$/u', '', $value);

        Assertion::string($trimmed, sprintf('Value "%s" cannot be trimmed', $value));

        return $trimmed;
    }
}
