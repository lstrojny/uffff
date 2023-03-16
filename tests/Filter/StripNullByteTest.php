<?php

declare(strict_types=1);

namespace Uffff\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Uffff\Filter\StripNullByte;

final class StripNullByteTest extends TestCase
{
    /**
     * @covers \Uffff\Filter\StripNullByte
     */
    public function testStripNullBytes(): void
    {
        $stripNullByte = new StripNullByte();

        self::assertSame('foo', $stripNullByte("\0f\0o\0o\0"));
    }
}
