<?php

declare(strict_types=1);

namespace Uffff\Tests\Builder;

use IntlChar;
use PHPUnit\Framework\TestCase;
use QuickCheck\Generator;
use QuickCheck\PHPUnit\PropertyConstraint;
use QuickCheck\Property;
use RuntimeException;
use Throwable;
use Uffff\Builder\FilterBuilder;
use Uffff\Value\Newline;
use Uffff\Value\NormalizationForm;
use Webmozart\Assert\Assert;

/**
 * @covers \Uffff\Builder\FilterBuilder
 */
final class FilterBuilderTest extends TestCase
{
    public function testDefaultBuilder(): void
    {
        $filter = (new FilterBuilder())
            ->build();

        self::assertSame("\u{00E4}foo\n\u{202A}bar\u{202C}", $filter(" \u{0061}\u{0308}foo\0\r\n\u{202A}bar\n\r "));
    }

    public function testWithCustomNormalization(): void
    {
        $filter = (new FilterBuilder())
            ->normalizeForm(NormalizationForm::NFD)
            ->build();

        self::assertSame("\u{0061}\u{0308}", $filter("\u{00E4}"));
    }

    public function testWithoutTrimWhitespace(): void
    {
        $filter = (new FilterBuilder())
            ->trimWhitespace(false)
            ->build();

        self::assertSame(' value ', $filter(' value '));
    }

    public function testWithCustomNewlines(): void
    {
        $filter = (new FilterBuilder())
            ->harmonizeNewlines(Newline::WINDOWS)
            ->build();

        self::assertSame("foo\r\nbar", $filter("foo\nbar"));
    }

    public function testRegisterCustomFilter(): void
    {
        $filter = (new FilterBuilder())
            ->add(static fn (string $v) => $v . '!')
            ->build();

        self::assertSame('Hello!', $filter('Hello'));
    }

    public function testShortCircuitsOnceChainProducesEmptyString(): void
    {
        $filter = (new FilterBuilder())
            ->add(static fn (string $v) => '')
            ->add(static fn (string $v) => throw new RuntimeException('Should not happen'))
            ->build();

        self::assertSame('', $filter('something'));
    }

    /**
     * @group expensive
     */
    public function testPropertyBasedTest(): void
    {
        $property = Property::forAll(
            [Generator::choose(0x0, 0x10FFFF)],
            static function (int $dec): bool {
                $char = IntlChar::chr($dec);
                Assert::string($char);

                try {
                    $output = ((new FilterBuilder())->build())($char);
                } catch (Throwable $e) {
                    // Surrogates should explode as they are invalid UTF-8
                    if (in_array(
                        IntlChar::getBlockCode($dec),
                        [
                            IntlChar::BLOCK_CODE_LOW_SURROGATES,
                            IntlChar::BLOCK_CODE_HIGH_SURROGATES,
                            IntlChar::BLOCK_CODE_HIGH_PRIVATE_USE_SURROGATES,
                        ],
                        true
                    )) {
                        return true;
                    }

                    throw $e;
                }

                $bidiFormat = in_array(
                    IntlChar::charDirection($dec),
                    [
                        IntlChar::CHAR_DIRECTION_RIGHT_TO_LEFT_OVERRIDE,
                        IntlChar::CHAR_DIRECTION_RIGHT_TO_LEFT_EMBEDDING,
                        IntlChar::CHAR_DIRECTION_LEFT_TO_RIGHT_OVERRIDE,
                        IntlChar::CHAR_DIRECTION_LEFT_TO_RIGHT_EMBEDDING,
                    ],
                    true
                );
                $bidiIsolate = in_array(
                    IntlChar::charDirection($dec),
                    [
                        IntlChar::CHAR_DIRECTION_RIGHT_TO_LEFT_ISOLATE,
                        IntlChar::CHAR_DIRECTION_LEFT_TO_RIGHT_ISOLATE,
                        IntlChar::CHAR_DIRECTION_FIRST_STRONG_ISOLATE,
                    ],
                    true
                );
                $bidiPop = in_array(
                    IntlChar::charDirection($dec),
                    [
                        IntlChar::CHAR_DIRECTION_POP_DIRECTIONAL_FORMAT,
                        IntlChar::CHAR_DIRECTION_POP_DIRECTIONAL_ISOLATE,
                    ],
                    true
                );

                $spaceLike = IntlChar::isspace($dec) || IntlChar::iscntrl($dec);

                $result = ($spaceLike && $output === '') ||
                    (!normalizer_is_normalized($char) && $output === normalizer_normalize($char))
                    || ($bidiFormat && $output === $char . "\u{202C}")
                    || ($bidiIsolate && $output === $char . "\u{2069}")
                    || ($bidiPop && $output === '')
                    || $output === $char;

                Assert::true(
                    $result,
                    sprintf(
                        'Property must hold true for "%s" (0x%s), got output "%s" (0x%s)',
                        $char,
                        dechex($dec),
                        $output,
                        bin2hex($output)
                    )
                );

                return $result;
            },
            500_000
        );

        $this->assertThat($property, PropertyConstraint::check(500_000));
    }
}
