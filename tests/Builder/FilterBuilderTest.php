<?php

declare(strict_types=1);

namespace Uffff\Tests\Builder;

use IntlChar;
use PHPUnit\Framework\TestCase;
use QuickCheck\Generator;
use QuickCheck\PHPUnit\PropertyConstraint;
use QuickCheck\Property;
use Throwable;
use Uffff\Builder\FilterBuilder;
use Uffff\Value\Newline;
use Uffff\Value\NormalizationForm;
use Webmozart\Assert\Assert;

final class FilterBuilderTest extends TestCase
{
    /**
     * @covers \Uffff\Builder\FilterBuilder::build
     */
    public function testDefaultBuilder(): void
    {
        $filter = (new FilterBuilder())
            ->build();

        self::assertSame("\u{00E4}foo\n\u{202A}bar\u{202C}", $filter(" \u{0061}\u{0308}foo\r\n\u{202A}bar\n\r "));
    }

    /**
     * @covers \Uffff\Builder\FilterBuilder::normalizeForm
     */
    public function testWithCustomNormalization(): void
    {
        $filter = (new FilterBuilder())
            ->normalizeForm(NormalizationForm::NFD)
            ->build();

        self::assertSame("\u{0061}\u{0308}", $filter("\u{00E4}"));
    }

    /**
     * @covers \Uffff\Builder\FilterBuilder::trimWhitespace
     */
    public function testWithoutTrimWhitespace(): void
    {
        $filter = (new FilterBuilder())
            ->trimWhitespace(false)
            ->build();

        self::assertSame(' value ', $filter(' value '));
    }

    /**
     * @covers \Uffff\Builder\FilterBuilder::harmonizeNewlines
     */
    public function testWithCustomNewlines(): void
    {
        $filter = (new FilterBuilder())
            ->harmonizeNewlines(Newline::WINDOWS)
            ->build();

        self::assertSame("foo\r\nbar", $filter("foo\nbar"));
    }

    /**
     * @covers \Uffff\Builder\FilterBuilder::add
     */
    public function testRegisterCustomFilter(): void
    {
        $filter = (new FilterBuilder())
            ->add(fn (string $v) => $v . '!')
            ->build();

        self::assertSame('Hello!', $filter('Hello'));
    }

    /**
     * @covers \Uffff\Builder\FilterBuilder
     * @group expensive
     */
    public function testPropertyBasedTest(): void
    {
        $property = Property::forAll(
            [Generator::choose(0x0, 0x10FFFF)],
            function (int $dec): bool {
                $char = IntlChar::chr($dec);
                Assert::string($char);

                try {
                    $output = ((new FilterBuilder())->build())($char);
                } catch (Throwable $e) {
                    // Low surrogates should explode
                    if ($dec >= 0xDC00 && $dec <= 0xDFFF) {
                        return true;
                    }

                    // High surrogates should explode
                    if ($dec >= 0xD800 && $dec <= 0xDB7F) {
                        return true;
                    }

                    // Private use high surrogates should explode
                    if ($dec >= 0xDB80 && $dec <= 0xDBFF) {
                        return true;
                    }

                    throw $e;
                }

                $bidi = in_array(
                    IntlChar::charDirection($dec),
                    [
                        IntlChar::CHAR_DIRECTION_RIGHT_TO_LEFT_OVERRIDE,
                        IntlChar::CHAR_DIRECTION_RIGHT_TO_LEFT_EMBEDDING,
                        IntlChar::CHAR_DIRECTION_LEFT_TO_RIGHT_OVERRIDE,
                        IntlChar::CHAR_DIRECTION_LEFT_TO_RIGHT_EMBEDDING,
                    ],
                    true
                );

                $spaceLike = IntlChar::isspace($dec) || IntlChar::iscntrl($dec);

                $result = ($spaceLike && $output === '') ||
                    (! normalizer_is_normalized($char) && $output === normalizer_normalize($char))
                    || ($bidi && $output === $char . "\u{202C}")
                    || ($char === "\u{202C}" && $output === '')
                    || $output === $char;

                Assert::true(
                    $result,
                    sprintf('Property must hold true for "%s" (%d), got out put "%s"', $char, $dec, $output)
                );

                return $result;
            }
        );

        $this->assertThat($property, PropertyConstraint::check(500_000));
    }
}
