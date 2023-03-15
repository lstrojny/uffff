<?php

declare(strict_types=1);

namespace Uffff\Value;

/**
 * @internal
 */
enum BidirectionalMarker: string
{
    case LRE = "\u{202A}";
    case RLE = "\u{202B}";
    case LRO = "\u{202D}";
    case RLO = "\u{202E}";
    case PDF = "\u{202C}";

    /**
     * @psalm-pure
     */
    public static function characters(): string
    {
        return self::LRE->value . self::RLE->value . self::LRO->value . self::RLO->value . self::PDF ->value;
    }
}
