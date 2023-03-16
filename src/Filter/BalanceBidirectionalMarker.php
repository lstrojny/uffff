<?php

declare(strict_types=1);

namespace Uffff\Filter;

use Uffff\Contracts\Filter;
use Uffff\Value\BidirectionalMarker;

/**
 * @psalm-immutable
 * @internal
 */
readonly final class BalanceBidirectionalMarker implements Filter
{
    /**
     * @phpstan-pure
     */
    public function __invoke(string $value): string
    {
        // Based on http://www.iamcal.com/understanding-bidirectional-text/
        $pops = [
            BidirectionalMarker::POP_DIRECTIONAL_FORMATTING->value => 0,
            BidirectionalMarker::POP_DIRECTIONAL_ISOLATE->value => 0,
        ];

        $cleaned = preg_replace_callback(
            '/[' . BidirectionalMarker::characters() . ']/u',
            static function ($match) use (&$pops) {
                /** @var array<string, int> $pops */
                [$marker] = $match;
                $pop = BidirectionalMarker::getPopChar($marker);
                return match ($pop) {
                    default => [++$pops[$pop], $marker][1],
                    null => match ($pops[$marker]) {
                        0 => '',
                        default => [--$pops[$marker], $marker][1],
                    }
                };
            },
            $value
        );

        /** @var array<string, int> $pops */
        return $cleaned . implode('', array_map(static fn ($c) => str_repeat($c, $pops[$c]), array_keys($pops)));
    }
}
