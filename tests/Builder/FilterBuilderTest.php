<?php

declare(strict_types=1);

namespace Ufff\Tests\Builder;

use PHPUnit\Framework\TestCase;
use Uffff\Builder\FilterBuilder;
use Uffff\Value\NormalizationForm;

final class FilterBuilderTest extends TestCase
{
    /**
     * @covers \Uffff\Builder\FilterBuilder::build
     */
    public function testDefaultBuilder(): void
    {
        $filter = (new FilterBuilder())->build();

        self::assertSame("\u{00E4}foo\u{202A}bar\u{202C}", $filter(" \u{0061}\u{0308}foo\u{202A}bar "));
    }

    /**
     * @covers \Uffff\Builder\FilterBuilder::normalize
     */
    public function testWithCustomNormalization(): void
    {
        $filter = (new FilterBuilder())->normalize(NormalizationForm::NFD)->build();

        self::assertSame("\u{0061}\u{0308}", $filter("\u{00E4}"));
    }

    /**
     * @covers \Uffff\Builder\FilterBuilder::trimWhitespace
     */
    public function testWithoutTrimWhitespace(): void
    {
        $filter = (new FilterBuilder())->trimWhitespace(false)
            ->build();

        self::assertSame(' value ', $filter(' value '));
    }

    /**
     * @covers \Uffff\Builder\FilterBuilder::add
     */
    public function testRegisterCustomFilter(): void
    {
        $filter = (new FilterBuilder())->add(fn (string $v) => $v . '!')->build();

        self::assertSame('Hello!', $filter('Hello'));
    }
}
