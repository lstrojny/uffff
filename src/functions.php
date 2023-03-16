<?php

declare(strict_types=1);

namespace Uffff;

use Uffff\Builder\FilterBuilder;

/**
 * @phpstan-pure
 * @psalm-immutable
 */
function unicode(string $value): string
{
    /** @var (callable(string): string)|null $filter */
    static $filter = null;

    $filter ??= (new FilterBuilder())
        ->build();

    return $filter($value);
}

/**
 * @phpstan-pure
 * @psalm-immutable
 * @return ($value is null ? null : string)
 */
function unicode_or_null(?string $value): ?string
{
    if ($value === null) {
        return null;
    }

    return unicode($value);
}

/**
 * @phpstan-pure
 * @psalm-immutable
 */
function unicode_untrimmed(string $value): string
{
    /** @var (callable(string): string)|null $filter */
    static $filter = null;

    $filter ??= (new FilterBuilder())
        ->trimWhitespace(false)
        ->build();

    return $filter($value);
}

/**
 * @phpstan-pure
 * @psalm-immutable
 * @return ($value is null ? null : string)
 */
function unicode_untrimmed_or_null(?string $value): ?string
{
    if ($value === null) {
        return null;
    }

    return unicode_untrimmed($value);
}
