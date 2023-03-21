<?php

declare(strict_types=1);

namespace Uffff\Tests\Filter;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Uffff\Filter\AssertWellFormedUnicode;
use Uffff\Value\ValueContext;

/**
 * @covers \Uffff\Filter\AssertWellFormedUnicode
 */
final class AssertWellFormedUnicodeTest extends TestCase
{
    /**
     * @return array<string, array{string}>
     */
    public static function unicode(): array
    {
        return [
            'Empty value' => [''],
            'Some value' => ['value'],
            'Emoji' => ['👨‍👩‍👧‍👧'],
            'Ginger emoji from Unicode 15.0' => ["\u{1FADA}"],
        ];
    }

    /**
     * @dataProvider unicode
     */
    public function testCheckIfUnicode(string $value): void
    {
        $assertWellFormedUnicode = new AssertWellFormedUnicode(ValueContext::INPUT);

        self::assertSame($value, $assertWellFormedUnicode($value));
    }

    public function testThrowsExceptionOnInvalidUnicode(): void
    {
        $assertWellFormedUnicode = new AssertWellFormedUnicode(ValueContext::OUTPUT);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Output value.*\(c0\) contains non-unicode characters/');

        /** @psalm-suppress UnusedMethodCall */
        $assertWellFormedUnicode(chr(0xC0));
    }
}
