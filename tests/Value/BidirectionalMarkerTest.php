<?php

declare(strict_types=1);

namespace Uffff\Value;

use PHPUnit\Framework\TestCase;

final class BidirectionalMarkerTest extends TestCase
{
    /**
     * @return array<string, array{BidirectionalMarker}>
     */
    public static function markers(): array
    {
        $markers = [];
        foreach (BidirectionalMarker::cases() as $case) {
            $markers[$case->name] = [$case];
        }

        return $markers;
    }

    /**
     * @covers \Uffff\Value\BidirectionalMarker::characters
     *@dataProvider markers
     */
    public function testCharactersReturnListOfAllMarkerCharacters(BidirectionalMarker $case): void
    {
        self::assertStringContainsString($case->value, BidirectionalMarker::characters());
    }
}
