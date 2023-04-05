<?php

declare(strict_types=1);

namespace Uffff\Tests;

use PHPUnit\Framework\TestCase;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionConstant;
use Roave\BetterReflection\Reflection\ReflectionFunction;
use Roave\BetterReflection\Reflection\ReflectionMethod;
use Roave\BetterReflection\Reflector\DefaultReflector;
use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\Composer\Factory\MakeLocatorForComposerJson;
use Roave\BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;
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
                ['method', 'Uffff\\Builder\\FilterBuilder::add'],
                ['method', 'Uffff\\Builder\\FilterBuilder::build'],
                ['method', 'Uffff\\Builder\\FilterBuilder::harmonizeNewlines'],
                ['method', 'Uffff\\Builder\\FilterBuilder::normalizeForm'],
                ['method', 'Uffff\\Builder\\FilterBuilder::trimWhitespace'],
                ['method', 'Uffff\\Contract\\Filter::__invoke'],
                ['method', 'Uffff\\Value\\Newline::cases'],
                ['method', 'Uffff\\Value\\Newline::from'],
                ['method', 'Uffff\\Value\\Newline::tryFrom'],
                ['method', 'Uffff\\Value\\NormalizationForm::cases'],
            ],
            self::getPublishedSymbols()
        );
    }

    /**
     * @return list<array{string, string}>
     */
    private static function getPublishedSymbols(): array
    {
        $betterReflection = new BetterReflection();
        $reflector = new DefaultReflector(
            new AggregateSourceLocator(
                [
                    (new MakeLocatorForComposerJson())(__DIR__ . '/../', $betterReflection->astLocator()),
                    new PhpInternalSourceLocator($betterReflection->astLocator(), $betterReflection->sourceStubber()),
                ]
            )
        );

        $published = [
            ...self::filterPublished($reflector->reflectAllClasses(), 'class'),
            ...self::filterPublished($reflector->reflectAllFunctions(), 'function'),
            ...self::filterPublished($reflector->reflectAllConstants(), 'constant'),
        ];

        $published = array_unique($published, SORT_REGULAR);

        sort($published);

        return $published;
    }

    /**
     * @param iterable<ReflectionFunction|ReflectionClass|ReflectionConstant|ReflectionMethod> $reflections
     * @return list<array{string, string}>
     */
    private static function filterPublished(iterable $reflections, string $type): array
    {
        $published = [];

        foreach ($reflections as $reflection) {
            if ($reflection->getDocComment() === null || !str_contains($reflection->getDocComment(), " @internal\n")) {
                $name = $reflection->getName();
                if ($reflection instanceof ReflectionMethod) {
                    if (!$reflection->isPublic()) {
                        continue;
                    }

                    $name = $reflection->getDeclaringClass()
                        ->getName() . '::' . $name;
                }

                $published[] = [$type, $name];

                if ($reflection instanceof ReflectionClass) {
                    $published = array_merge($published, self::filterPublished($reflection->getMethods(), 'method'));
                }
            }
        }

        return $published;
    }
}
