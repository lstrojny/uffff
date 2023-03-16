<?php

declare(strict_types=1);

namespace Uffff\Tests\Builder;

use PHPUnit\Framework\TestCase;
use Uffff\Builder\FilterBuilder;
use Uffff\Value\Newline;
use Uffff\Value\NormalizationForm;

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
}
