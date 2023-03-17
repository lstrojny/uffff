<?php

declare(strict_types=1);

namespace Uffff\Tests\Builder\Fixture;

use Uffff\Contract\Filter;

final class Overlapping implements Filter
{
    public function __invoke(string $text): string
    {
        return $text;
    }
}
