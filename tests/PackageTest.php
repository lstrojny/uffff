<?php

declare(strict_types=1);

namespace Uffff\Tests;

use PHPUnit\Framework\TestCase;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionConstant;
use Roave\BetterReflection\Reflection\ReflectionFunction;
use Roave\BetterReflection\Reflector\DefaultReflector;
use Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;
use Uffff\Builder\FilterBuilder;
use Uffff\Contract\Filter;
use Uffff\Value\Newline;
use Uffff\Value\NormalizationForm;

/**
 * @coversNothing
 */
final class PackageTest extends TestCase
{
    public function testPublishedApiIsWellDefined(): void
    {
        self::assertSame(
            [
                ['class', FilterBuilder::class],
                ['class', Filter::class],
                ['class', Newline::class],
                ['class', NormalizationForm::class],
                ['function', 'Uffff\\unicode'],
                ['function', 'Uffff\\unicode_or_null'],
                ['function', 'Uffff\\unicode_untrimmed'],
                ['function', 'Uffff\\unicode_untrimmed_or_null'],
            ],
            self::getPublishedSymbols()
        );
    }

    /**
     * @return list<array{string, string}>
     */
    private static function getPublishedSymbols(): array
    {
        $astLocator = (new BetterReflection())->astLocator();
        $directoriesSourceLocator = new DirectoriesSourceLocator([__DIR__ . '/../src'], $astLocator);
        $reflector = new DefaultReflector($directoriesSourceLocator);

        $published = [
            ...self::filterPublished($reflector->reflectAllClasses(), 'class'),
            ...self::filterPublished($reflector->reflectAllFunctions(), 'function'),
            ...self::filterPublished($reflector->reflectAllConstants(), 'constant'),
        ];

        sort($published);

        return $published;
    }

    /**
     * @param iterable<ReflectionFunction|ReflectionClass|ReflectionConstant> $reflections
     * @return list<array{string, string}>
     */
    private static function filterPublished(iterable $reflections, string $type): array
    {
        $published = [];

        foreach ($reflections as $reflection) {
            if ($reflection->getDocComment() === null || !str_contains($reflection->getDocComment(), " @internal\n")) {
                $published[] = [$type, $reflection->getName()];
            }
        }

        return $published;
    }
}
