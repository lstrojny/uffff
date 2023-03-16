<?php

declare(strict_types=1);

namespace Uffff\Filter;

use Uffff\Contract\Filter;

/**
 * @psalm-immutable
 * @internal
 */
readonly final class StripNullByte implements Filter
{
    /**
     * @phpstan-pure
     */
    public function __invoke(string $text): string
    {
        return str_replace(chr(0x0), '', $text);
    }
}
