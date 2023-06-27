<?php

declare(strict_types=1);

namespace Foo;

use Uffff\Builder\FilterBuilder;

function unicode(string $text): string
{
    $filter = (new FilterBuilder())->build();

    return $filter($text);
}
