<?php

declare(strict_types=1);

namespace Uffff\Tests\Filter;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Uffff\Filter\AssertWellFormedUnicode;

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
        $assertWellFormedUnicode = new AssertWellFormedUnicode();

        self::assertSame($value, $assertWellFormedUnicode($value));
    }

    public function testThrowsExceptionOnInvalidUnicode(): void
    {
        $assertWellFormedUnicode = new AssertWellFormedUnicode();

        $this->expectException(InvalidArgumentException::class);

        /** @psalm-suppress UnusedMethodCall */
        $assertWellFormedUnicode(chr(0xC0));
    }
}
