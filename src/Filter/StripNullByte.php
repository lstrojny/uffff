<?php

declare(strict_types=1);

namespace Uffff\Filter;

use Uffff\Contract\Filter;

/**
 * @psalm-immutable
 * @internal
 */
final readonly class StripNullByte implements Filter
{
    private const NULL = "\0";

    /**
     * @psalm-pure
     */
    public function __invoke(string $text): string
    {
        return str_replace(self::NULL, '', $text);
    }
}
