<?php

declare(strict_types=1);

namespace Uffff\Contracts;

/**
 * @phpstan-pure
 * @psalm-immutable
 * @phpstan-type FilterFn callable(string): string
 */
interface Filter
{
    public function __invoke(string $value): string;
}
