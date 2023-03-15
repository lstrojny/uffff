<?php

declare(strict_types=1);

namespace Ufff\Builder;

use PHPUnit\Framework\TestCase;
use Uffff\Builder\FlyweightFactory;
use Uffff\Filter\CheckIfUnicode;

final class FlyweightFactoryTest extends TestCase
{
    /**
     * @covers \Uffff\Builder\FlyweightFactory::create
     */
    public function testCreateReturnsExistingInstance(): void
    {
        self::assertSame(
            FlyweightFactory::create(CheckIfUnicode::class),
            FlyweightFactory::create(CheckIfUnicode::class)
        );
    }
}
