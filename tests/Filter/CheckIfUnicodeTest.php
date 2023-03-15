<?php

declare(strict_types=1);

namespace Uffff\Tests\Filter;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Uffff\Filter\CheckIfUnicode;

final class CheckIfUnicodeTest extends TestCase
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
     * @covers \Uffff\Filter\CheckIfUnicode::__invoke
     */
    public function testCheckIfUnicode(string $value): void
    {
        $check = new CheckIfUnicode();

        self::assertSame($value, $check($value));
    }

    /**
     * @covers \Uffff\Filter\CheckIfUnicode::__invoke
     */
    public function testThrowsExceptionOnInvalidUnicode(): void
    {
        $check = new CheckIfUnicode();

        $this->expectException(InvalidArgumentException::class);

        /** @psalm-suppress UnusedMethodCall */
        $check(chr(0xC0));
    }
}
