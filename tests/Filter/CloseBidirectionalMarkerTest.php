<?php

declare(strict_types=1);

namespace Uffff\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Uffff\Filter\CloseBidirectionalMarker;

final class CloseBidirectionalMarkerTest extends TestCase
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
        ];

        $markers = [
            'LRE' => "\u{202A}",
            'RLE' => "\u{202B}",
            'LRO' => "\u{202D}",
            'RLO' => "\u{202E}",
        ];

        foreach ($markers as $marker => $codePoint) {
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

        return $cases;
    }

    /**
     * @covers \Uffff\Filter\CloseBidirectionalMarker::__invoke
     * @dataProvider markers
     */
    public function testBidirectionalMarkersAreClosed(string $input, string $output): void
    {
        $closeBidirectionalMarker = new CloseBidirectionalMarker();

        self::assertSame($output, $closeBidirectionalMarker($input));
    }
}
