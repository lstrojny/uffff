<?php

declare(strict_types=1);

namespace Uffff;

use Uffff\Builder\FilterBuilder;

/**
 * @phpstan-pure
 * @psalm-immutable
 */
function unicode(string $text): string
{
    /** @var (callable(string): string)|null $filter */
    static $filter = null;

    $filter ??= (new FilterBuilder())
        ->build();

    return $filter($text);
}

/**
 * @phpstan-pure
 * @psalm-immutable
 * @return ($text is null ? null : string)
 */
function unicode_or_null(?string $text): ?string
{
    if ($text === null) {
        return null;
    }

    return unicode($text);
}

/**
 * @phpstan-pure
 * @psalm-immutable
 */
function unicode_untrimmed(string $text): string
{
    /** @var (callable(string): string)|null $filter */
    static $filter = null;

    $filter ??= (new FilterBuilder())
        ->trimWhitespace(false)
        ->build();

    return $filter($text);
}

/**
 * @phpstan-pure
 * @psalm-immutable
 * @return ($text is null ? null : string)
 */
function unicode_untrimmed_or_null(?string $text): ?string
{
    if ($text === null) {
        return null;
    }

    return unicode_untrimmed($text);
}
