<?php

declare(strict_types=1);

namespace Uffff\Value;

/**
 * @internal
 */
enum BidirectionalMarker: string
{
    case LEFT_TO_RIGHT_EMBEDDING = "\u{202A}";
    case RIGHT_TO_LEFT_EMBEDDING = "\u{202B}";
    case LEFT_TO_RIGHT_OVERRIDE = "\u{202D}";
    case RIGHT_TO_LEFT_OVERRIDE = "\u{202E}";
    case POP_DIRECTIONAL_FORMATTING = "\u{202C}";

    case LEFT_TO_RIGHT_ISOLATE = "\u{2066}";
    case RIGHT_TO_LEFT_ISOLATE = "\u{2067}";
    case FIRST_STRONG_ISOLATE = "\u{2068}";
    case POP_DIRECTIONAL_ISOLATE = "\u{2069}";

    /**
     * @psalm-pure
     */
    public static function characters(): string
    {
        return ''
            . self::LEFT_TO_RIGHT_EMBEDDING->value
            . self::RIGHT_TO_LEFT_EMBEDDING->value
            . self::LEFT_TO_RIGHT_OVERRIDE->value
            . self::RIGHT_TO_LEFT_OVERRIDE->value
            . self::POP_DIRECTIONAL_FORMATTING->value
            . self::LEFT_TO_RIGHT_ISOLATE->value
            . self::RIGHT_TO_LEFT_ISOLATE->value
            . self::FIRST_STRONG_ISOLATE->value
            . self::POP_DIRECTIONAL_ISOLATE->value;
    }

    /**
     * @psalm-pure
     */
    public static function getPopChar(string $opener): ?string
    {
        return match (self::tryFrom($opener)) {
            self::LEFT_TO_RIGHT_EMBEDDING, self::RIGHT_TO_LEFT_EMBEDDING , self::LEFT_TO_RIGHT_OVERRIDE , self::RIGHT_TO_LEFT_OVERRIDE => self::POP_DIRECTIONAL_FORMATTING->value,
            self::LEFT_TO_RIGHT_ISOLATE, self::RIGHT_TO_LEFT_ISOLATE, self::FIRST_STRONG_ISOLATE => self::POP_DIRECTIONAL_ISOLATE->value,
            default => null,
        };
    }
}
