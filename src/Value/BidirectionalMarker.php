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
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (BidirectionalMarker $v) => $v->value, self::cases());
    }
}
