<?php

declare(strict_types=1);

namespace Uffff\Tests\Filter;

use IntlChar;
use PHPUnit\Framework\TestCase;
use Uffff\Filter\TrimWhitespace;
use Webmozart\Assert\Assert;

final class TrimWhitespaceTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function trim(): array
    {
        $spaces = [
            ' ',
            "\t",
            "\n",
            "\r",
            "\u{0020}",
            "\u{00A0}",
            "\u{1680}",
            "\u{2000}",
            "\u{2001}",
            "\u{2002}",
            "\u{2003}",
            "\u{2004}",
            "\u{2005}",
            "\u{2006}",
            "\u{2007}",
            "\u{2008}",
            "\u{2009}",
            "\u{200A}",
            "\u{202F}",
            "\u{205F}",
            "\u{3000}",
        ];

        $examples = [
            'LRE is not trimmed' => ["\u{202A}v", "\u{202A}v"],
            'PDF is not trimmed' => ["\u{202C}v", "\u{202C}v"],
        ];

        foreach ($spaces as $space) {
            $examples[sprintf('%s left trim', self::charName($space))] = [sprintf('%svalue', $space), 'value'];
            $examples[sprintf('%s right trim', self::charName($space))] = [sprintf('value%s', $space), 'value'];
            $examples[sprintf('%s trim both', self::charName($space))] = [
                sprintf('%1$svalue%1$s', $space),
                'value',
            ];
            $examples[sprintf('%s trim multiple', self::charName($space))] = [
                sprintf('%1$s%1$s%1$svalue%1$s%1$s', $space),
                'value',
            ];
        }

        return $examples;
    }

    /**
     * @dataProvider trim
     * @covers \Uffff\Filter\TrimWhitespace::__invoke
     */
    public function testTrim(string $input, string $output): void
    {
        $trim = new TrimWhitespace();

        self::assertSame($output, $trim($input));
    }

    private static function charName(string $codePoint): string
    {
        $name = IntlChar::charName($codePoint);
        Assert::string($name);

        return $name;
    }
}
