<?php

declare(strict_types=1);

namespace Uffff\Tests\Filter;

use IntlChar;
use PHPUnit\Framework\TestCase;
use Uffff\Filter\BalanceBidirectionalMarker;
use Webmozart\Assert\Assert;

/**
 * @covers \Uffff\Filter\BalanceBidirectionalMarker
 */
final class BalanceBidirectionalMarkerTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function markers(): array
    {
        $cases = [
            'no markers' => ['some text', 'some text'],
            'dangling PDF start' => ["\u{202C}value", 'value'],
            'dangling PDF end' => ["value\u{202C}", 'value'],
            'mixed balanced' => ["\u{202A}foo\u{2066}bar\u{202C}\u{2069}", "\u{202A}foo\u{2066}bar\u{202C}\u{2069}"],
            'mixed unbalanced formatting' => [
                "\u{202A}foo\u{2066}bar\u{202C}",
                "\u{202A}foo\u{2066}bar\u{202C}\u{2069}",
            ],
            'mixed unbalanced directional' => [
                "\u{202A}foo\u{2066}bar\u{2069}",
                "\u{202A}foo\u{2066}bar\u{2069}\u{202C}",
            ],
            'mixed unbalanced both' => ["\u{202A}foo\u{2066}bar", "\u{202A}foo\u{2066}bar\u{202C}\u{2069}"],
        ];

        $directionalMarkers = ["\u{202A}", "\u{202B}", "\u{202D}", "\u{202E}"];

        foreach ($directionalMarkers as $codePoint) {
            $marker = IntlChar::charName($codePoint);
            Assert::string($marker);
            $cases[sprintf('dangling %s start', $marker)] = [
                sprintf('%svalue', $codePoint),
                sprintf("%svalue\u{202C}", $codePoint),
            ];
            $cases[sprintf('dangling %s end', $marker)] = [
                sprintf('value%s', $codePoint),
                sprintf("value%s\u{202C}", $codePoint),
            ];
            $cases[sprintf('nested %s', $marker)] = [
                sprintf('%1$svalue%1$s', $codePoint),
                sprintf("%1\$svalue%1\$s\u{202C}\u{202C}", $codePoint),
            ];
            $cases[sprintf('nested with balanced close %s', $marker)] = [
                sprintf("%1\$sfirst\u{202C}value%1\$s", $codePoint),
                sprintf("%1\$sfirst\u{202C}value%1\$s\u{202C}", $codePoint),
            ];
            $cases[sprintf('nested with unbalanced close %s', $marker)] = [
                sprintf("%1\$sfirst\u{202C}v\u{202C}alue%1\$s", $codePoint),
                sprintf("%1\$sfirst\u{202C}value%1\$s\u{202C}", $codePoint),
            ];
        }

        $isolateMarkers = ["\u{2066}", "\u{2067}", "\u{2068}"];

        foreach ($isolateMarkers as $codePoint) {
            $marker = IntlChar::charName($codePoint);
            Assert::string($marker);
            $cases[sprintf('dangling %s start', $marker)] = [
                sprintf('%svalue', $codePoint),
                sprintf("%svalue\u{2069}", $codePoint),
            ];
            $cases[sprintf('dangling %s end', $marker)] = [
                sprintf('value%s', $codePoint),
                sprintf("value%s\u{2069}", $codePoint),
            ];
            $cases[sprintf('nested %s', $marker)] = [
                sprintf('%1$svalue%1$s', $codePoint),
                sprintf("%1\$svalue%1\$s\u{2069}\u{2069}", $codePoint),
            ];
            $cases[sprintf('nested with balanced close %s', $marker)] = [
                sprintf("%1\$sfirst\u{2069}value%1\$s", $codePoint),
                sprintf("%1\$sfirst\u{2069}value%1\$s\u{2069}", $codePoint),
            ];
            $cases[sprintf('nested with unbalanced close %s', $marker)] = [
                sprintf("%1\$sfirst\u{2069}v\u{2069}alue%1\$s", $codePoint),
                sprintf("%1\$sfirst\u{2069}value%1\$s\u{2069}", $codePoint),
            ];
        }

        return $cases;
    }

    /**
     * @dataProvider markers
     */
    public function testBidirectionalMarkersAreClosed(string $input, string $output): void
    {
        $balanceBidirectionalMarker = new BalanceBidirectionalMarker();

        self::assertSame($output, $balanceBidirectionalMarker($input));
    }
}
