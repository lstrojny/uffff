<?php

declare(strict_types=1);

namespace Uffff\Tests;

use PHPUnit\Framework\TestCase;
use function Uffff\unicode;
use function Uffff\unicode_or_null;
use function Uffff\unicode_untrimmed;
use function Uffff\unicode_untrimmed_or_null;

final class FunctionTest extends TestCase
{
    /**
     * @covers \Uffff\unicode
     */
    public function testFilterUnicode(): void
    {
        self::assertSame("foo\u{202A}bar\u{202C}", unicode(" foo\u{202A}bar "));
    }

    /**
     * @covers \Uffff\unicode_or_null
     */
    public function testFilterUnicodeOrNull(): void
    {
        self::assertNull(unicode_or_null(null));
    }

    /**
     * @covers \Uffff\unicode_untrimmed
     */
    public function testFilterUnicodeUntrimmed(): void
    {
        self::assertSame("foo\u{202A}bar \u{202C}", unicode_untrimmed("foo\u{202A}bar "));
    }

    /**
     * @covers \Uffff\unicode_untrimmed_or_null
     */
    public function testFilterUnicodeUntrimmedOrNull(): void
    {
        self::assertNull(unicode_untrimmed_or_null(null));
    }
}
