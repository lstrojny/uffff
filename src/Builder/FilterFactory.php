<?php

declare(strict_types=1);

namespace Uffff\Builder;

use RuntimeException;
use Uffff\Contract\Filter;
use Webmozart\Assert\Assert;

/**
 * Factory for filter object
 *
 * Acts as a flyweight to avoid unnecessarily duplicating filter objects.
 *
 * @internal
 * @phpstan-import-type FilterFn from Filter
 */
final class FilterFactory
{
    /**
     * @var array<string, Filter>
     */
    private static array $registry = [];

    /**
     * Private constructor - class is not meant to be instantiated
     */
    private function __construct()
    {
        throw new RuntimeException('Class cannot be instantiated');
    }

    /**
     * Class is not meant to be cloned
     */
    private function __clone()
    {
        throw new RuntimeException('Class cannot be cloned');
    }

    /**
     * @template ConcreteFilter of Filter
     * @psalm-pure
     * @param class-string<ConcreteFilter> $className
     * @return FilterFn
     */
    public static function create(string $className): callable
    {
        return self::getOrCreate($className, $className, []);
    }

    /**
     * @template ConcreteFilter of Filter
     * @param class-string<ConcreteFilter> $className
     * @psalm-pure
     * @param list<mixed> $arguments
     * @return FilterFn
     */
    public static function createWith(string $className, array $arguments, string $key): callable
    {
        return self::getOrCreate($className . '$' . $key, $className, $arguments);
    }

    /**
     * @template ConcreteFilter of Filter
     * @psalm-pure
     * @param class-string<ConcreteFilter> $className
     * @param list<mixed> $arguments
     * @return ConcreteFilter
     */
    private static function getOrCreate(string $registryKey, string $className, array $arguments): callable
    {
        /** @psalm-suppress ImpureStaticProperty */
        $instance = self::$registry[$registryKey] ??= new $className(...$arguments);

        Assert::isInstanceOf($instance, $className);
        Assert::isInstanceOf($instance, Filter::class);
        Assert::isCallable($instance);

        return $instance;
    }
}
