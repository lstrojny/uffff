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
     * @covers \Uffff\Value\BidirectionalMarker
     * @dataProvider markers
     */
    public function testCharactersReturnListOfAllMarkerCharacters(BidirectionalMarker $case): void
    {
        self::assertStringContainsString($case->value, BidirectionalMarker::CHARACTERS);
    }

    /**
     * @covers \Uffff\Value\BidirectionalMarker::getPopChar
     */
    public function testPopCharOfUnknownCharacterReturnsNull(): void
    {
        self::assertNull(BidirectionalMarker::getPopChar('a'));
    }

    /**
     * @return list<array{BidirectionalMarker, BidirectionalMarker}>
     */
    public static function pushAndPopChars(): array
    {
        return [
            [BidirectionalMarker::LEFT_TO_RIGHT_EMBEDDING, BidirectionalMarker::POP_DIRECTIONAL_FORMATTING],
            [BidirectionalMarker::LEFT_TO_RIGHT_OVERRIDE, BidirectionalMarker::POP_DIRECTIONAL_FORMATTING],
            [BidirectionalMarker::RIGHT_TO_LEFT_EMBEDDING, BidirectionalMarker::POP_DIRECTIONAL_FORMATTING],
            [BidirectionalMarker::RIGHT_TO_LEFT_OVERRIDE, BidirectionalMarker::POP_DIRECTIONAL_FORMATTING],
            [BidirectionalMarker::LEFT_TO_RIGHT_ISOLATE, BidirectionalMarker::POP_DIRECTIONAL_ISOLATE],
            [BidirectionalMarker::RIGHT_TO_LEFT_ISOLATE, BidirectionalMarker::POP_DIRECTIONAL_ISOLATE],
            [BidirectionalMarker::FIRST_STRONG_ISOLATE, BidirectionalMarker::POP_DIRECTIONAL_ISOLATE],
        ];
    }

    /**
     * @dataProvider pushAndPopChars
     * @covers \Uffff\Value\BidirectionalMarker::getPopChar
     */
    public function testPopCharOfPushCharacters(BidirectionalMarker $push, BidirectionalMarker $pop): void
    {
        self::assertSame($pop->value, BidirectionalMarker::getPopChar($push->value));
    }

    /**
     * @return list<array{BidirectionalMarker}>
     */
    public static function popChars(): array
    {
        return [[BidirectionalMarker::POP_DIRECTIONAL_FORMATTING], [BidirectionalMarker::POP_DIRECTIONAL_ISOLATE]];
    }

    /**
     * @dataProvider popChars
     * @covers \Uffff\Value\BidirectionalMarker::getPopChar
     */
    public function testPopCharOfPopCharacters(BidirectionalMarker $pop): void
    {
        self::assertNull(BidirectionalMarker::getPopChar($pop->value));
    }
}
