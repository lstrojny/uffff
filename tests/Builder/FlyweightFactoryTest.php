<?php

declare(strict_types=1);

namespace Ufff\Builder;

use PHPUnit\Framework\TestCase;
use Uffff\Builder\FlyweightFactory;
use Uffff\Filter\CheckIfUnicode;

final class FlyweightFactoryTest extends TestCase
{
    /**
     * @covers \Uffff\Builder\FlyweightFactory
     */
    public function testCreateReturnsExistingInstance(): void
    {
        self::assertSame(
            FlyweightFactory::create(CheckIfUnicode::class),
            FlyweightFactory::create(CheckIfUnicode::class)
        );
    }

    /**
     * @covers \Uffff\Builder\FlyweightFactory
     */
    public function testCreateWithReturnsExistingInstanceIfKeysMatch(): void
    {
        self::assertSame(
            FlyweightFactory::createWith(CheckIfUnicode::class, [], 'foo'),
            FlyweightFactory::createWith(CheckIfUnicode::class, [], 'foo')
        );
    }
}
