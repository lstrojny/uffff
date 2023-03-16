<?php

declare(strict_types=1);

namespace Uffff\Filter;

use Normalizer;
use Uffff\Contracts\Filter;
use Uffff\Value\NormalizationForm;
use Webmozart\Assert\Assert;

/**
 * @psalm-immutable
 * @internal
 */
readonly final class StripNullByte implements Filter
{
    public function __invoke(string $value): string
    {
        return str_replace(chr(0x0), '', $value);
    }
}