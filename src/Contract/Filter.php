<?php

declare(strict_types=1);

namespace Uffff\Contract;

/**
 * @psalm-immutable
 * @psalm-readonly
 * @phpstan-type FilterFn (pure-callable(string): string)
 */
interface Filter
{
    public function __invoke(string $text): string;
}
