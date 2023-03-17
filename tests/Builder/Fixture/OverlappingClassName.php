<?php

declare(strict_types=1);

namespace Uffff\Tests\Builder\Fixture;

use Uffff\Contract\Filter;

class OverlappingClassName implements Filter
{

    public function __invoke(string $text): string
    {
        return $text;
    }
}