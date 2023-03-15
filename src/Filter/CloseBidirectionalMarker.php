<?php

declare(strict_types=1);

namespace Uffff\Filter;

use Uffff\Contracts\Filter;
use Uffff\Value\BidirectionalMarker;

/**
 * @psalm-immutable
 */
readonly final class CloseBidirectionalMarker implements Filter
{
    public function __invoke(string $value): string
    {
        // Based on http://www.iamcal.com/understanding-bidirectional-text/
        $pdf = BidirectionalMarker::PDF->value;
        $nestingLevel = 0;
        /** @psalm-suppress ImpureFunctionCall */
        return preg_replace_callback(
            '/[' . BidirectionalMarker::characters() . ']/u',
            function ($marker) use (&$nestingLevel, $pdf) {
                if ($marker[0] === $pdf) {
                    if ($nestingLevel === 0) {
                        return '';
                    }

                    $nestingLevel--;
                } else {
                    $nestingLevel++;
                }

                return $marker[0];
            },
            $value
        ) . str_repeat($pdf, $nestingLevel);
    }
}
