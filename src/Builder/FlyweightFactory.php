<?php


declare(strict_types=1);

namespace Uffff\Builder;

use Uffff\Contracts\Filter;
use Webmozart\Assert\Assert;

final class FlyweightFactory
{
    /**
     * @var array<string, Filter>
     */
    private static array $registry = [];

    /**
     * @psalm-suppress UnusedConstructor
     */
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @template ConcreteFilter of Filter
     * @param class-string<ConcreteFilter> $className
     * @return ConcreteFilter
     */
    public static function create(string $className): object
    {
        return self::getOrCreate($className, $className, []);
    }

    /**
     * @template ConcreteFilter of Filter
     * @param class-string<ConcreteFilter> $className
     * @param list<mixed> $arguments
     * @return ConcreteFilter
     */
    public static function createWith(string $className, array $arguments, string $key): object
    {
        return self::getOrCreate($className . '$' . $key, $className, $arguments);
    }

    /**
     * @template ConcreteFilter of Filter
     * @param class-string<ConcreteFilter> $className
     * @param list<mixed> $arguments
     * @return ConcreteFilter
     */
    private static function getOrCreate(string $registryKey, string $className, array $arguments): object
    {
        $instance = self::$registry[$registryKey] ??= new $className(...$arguments);

        Assert::isInstanceOf($instance, $className);

        return $instance;
    }
}
