<?php

declare(strict_types=1);

namespace Uffff\Tests\Builder;

use Error;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use Uffff\Builder\FilterFactory;
use Uffff\Filter\StripNullByte;
use Uffff\Filter\TrimWhitespace;
use Uffff\Tests\Builder\Fixture\Overlapping;
use Uffff\Tests\Builder\Fixture\OverlappingClassName;

/**
 * @covers \Uffff\Builder\FilterFactory
 */
final class FilterFactoryTest extends TestCase
{
    public function testCreateReturnsExistingInstance(): void
    {
        self::assertSame(FilterFactory::create(StripNullByte::class), FilterFactory::create(StripNullByte::class));
    }

    public function testCreateReturnsDifferentInstancesForDifferentClasses(): void
    {
        self::assertNotSame(
            FilterFactory::create(TrimWhitespace::class),
            FilterFactory::create(StripNullByte::class)
        );
    }

    public function testDoesNotConfuseClassNames(): void
    {
        self::assertNotSame(
            FilterFactory::createWith(OverlappingClassName::class, [], ''),
            FilterFactory::createWith(Overlapping::class, [], 'ClassName'),
        );
    }

    public function testCreateWithReturnsExistingInstanceIfKeysMatch(): void
    {
        self::assertSame(
            FilterFactory::createWith(TrimWhitespace::class, [], 'foo'),
            FilterFactory::createWith(TrimWhitespace::class, [], 'foo')
        );
    }

    public function testCreateWithReturnsNewInstanceIfKeysDontMatch(): void
    {
        self::assertNotSame(
            FilterFactory::createWith(StripNullByte::class, [], 'foo'),
            FilterFactory::createWith(StripNullByte::class, [], 'bar')
        );
    }

    public function testClassCannotBeInstantiatedBecauseConstructorVisibilityIsPrivate(): void
    {
        $this->expectException(Error::class);
        /**
         * @psalm-suppress InaccessibleMethod
         * @phpstan-ignore-next-line
         */
        new FilterFactory();
    }

    public function testClassCannotBeInstantiatedBecauseConstructorThrows(): void
    {
        $this->expectException(RuntimeException::class);
        /**
         * @psalm-suppress InaccessibleMethod,PossiblyNullFunctionCall
         * @phpstan-ignore-next-line
         */
        (static fn (): mixed => new FilterFactory())
            ->bindTo(null, FilterFactory::class)();
    }

    public function testClassCannotBeClonedBecauseCloneMethodVisibilityIsPrivate(): void
    {
        $this->expectException(Error::class);
        /** @psalm-suppress InvalidClone */
        clone (new ReflectionClass(FilterFactory::class))->newInstanceWithoutConstructor();
    }

    public function testClassCannotBeClonedBecauseCloneThrows(): void
    {
        $this->expectException(RuntimeException::class);
        /** @psalm-suppress InvalidClone,PossiblyNullFunctionCall */
        (static fn (): mixed => clone (new ReflectionClass(
            FilterFactory::class
        ))->newInstanceWithoutConstructor())->bindTo(null, FilterFactory::class)();
    }
}
