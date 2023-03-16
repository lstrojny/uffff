<?php

declare(strict_types=1);

namespace Uffff\Filter;

use Uffff\Contracts\Filter;

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
