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
    private const NULL = "\0";

    /**
     * @phpstan-pure
     */
    public function __invoke(string $text): string
    {
        return str_replace(self::NULL, '', $text);
    }
}
