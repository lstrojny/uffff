<?php

declare(strict_types=1);

namespace Uffff\Tests\Builder;

use Error;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use Uffff\Builder\FlyweightFactory;
use Uffff\Filter\AssertWellFormedUnicode;
use Uffff\Filter\StripNullByte;
use Uffff\Tests\Builder\Fixture\Overlapping;
use Uffff\Tests\Builder\Fixture\OverlappingClassName;

/**
 * @covers \Uffff\Builder\FlyweightFactory
 */
final class FlyweightFactoryTest extends TestCase
{
    public function testCreateReturnsExistingInstance(): void
    {
        self::assertSame(
            FlyweightFactory::create(AssertWellFormedUnicode::class),
            FlyweightFactory::create(AssertWellFormedUnicode::class)
        );
    }

    public function testCreateReturnsDifferentInstancesForDifferentClasses(): void
    {
        self::assertNotSame(
            FlyweightFactory::create(AssertWellFormedUnicode::class),
            FlyweightFactory::create(StripNullByte::class)
        );
    }

    public function testDoesNotConfuseClassNames(): void
    {
        self::assertNotSame(
            FlyweightFactory::createWith(OverlappingClassName::class, [], ''),
            FlyweightFactory::createWith(Overlapping::class, [], 'ClassName'),
        );
    }

    public function testCreateWithReturnsExistingInstanceIfKeysMatch(): void
    {
        self::assertSame(
            FlyweightFactory::createWith(AssertWellFormedUnicode::class, [], 'foo'),
            FlyweightFactory::createWith(AssertWellFormedUnicode::class, [], 'foo')
        );
    }

    public function testCreateWithReturnsNewInstanceIfKeysDontMatch(): void
    {
        self::assertNotSame(
            FlyweightFactory::createWith(StripNullByte::class, [], 'foo'),
            FlyweightFactory::createWith(StripNullByte::class, [], 'bar')
        );
    }

    public function testClassCannotBeInstantiatedBecauseConstructorVisibilityIsPrivate(): void
    {
        $this->expectException(Error::class);
        /**
         * @psalm-suppress InaccessibleMethod
         * @phpstan-ignore-next-line
         */
        new FlyweightFactory();
    }

    public function testClassCannotBeInstantiatedBecauseConstructorThrows(): void
    {
        $this->expectException(RuntimeException::class);
        /**
         * @psalm-suppress InaccessibleMethod,PossiblyNullFunctionCall
         * @phpstan-ignore-next-line
         */
        (static fn (): mixed => new FlyweightFactory())
            ->bindTo(null, FlyweightFactory::class)();
    }

    public function testClassCannotBeClonedBecauseCloneMethodVisibilityIsPrivate(): void
    {
        $this->expectException(Error::class);
        /** @psalm-suppress InvalidClone */
        clone (new ReflectionClass(FlyweightFactory::class))->newInstanceWithoutConstructor();
    }

    public function testClassCannotBeClonedBecauseCloneThrows(): void
    {
        $this->expectException(RuntimeException::class);
        /** @psalm-suppress InvalidClone,PossiblyNullFunctionCall */
        (static fn (): mixed => clone (new ReflectionClass(
            FlyweightFactory::class
        ))->newInstanceWithoutConstructor())->bindTo(null, FlyweightFactory::class)();
    }
}
