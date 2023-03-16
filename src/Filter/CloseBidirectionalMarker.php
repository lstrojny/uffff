<?php

declare(strict_types=1);

namespace Uffff\Filter;

use Uffff\Contracts\Filter;
use Uffff\Value\BidirectionalMarker;
use Webmozart\Assert\Assert;

/**
 * @psalm-immutable
 * @internal
 */
readonly final class CloseBidirectionalMarker implements Filter
{
    public function __invoke(string $value): string
    {
        // Based on http://www.iamcal.com/understanding-bidirectional-text/
        $pdf = BidirectionalMarker::PDF->value;
        $nestingLevel = 0;
        /** @psalm-suppress ImpureFunctionCall */
        $cleaned = preg_replace_callback(
            '/[' . BidirectionalMarker::characters() . ']/u',
            function ($marker) use (&$nestingLevel, $pdf) {
                Assert::integer($nestingLevel, 'Make static analysis happy');

                if ($marker[0] === $pdf) {
                    if ($nestingLevel === 0) {
                        return '';
                    }

                    --$nestingLevel;
                } else {
                    ++$nestingLevel;
                }

                return $marker[0];
            },
            $value
        );

        Assert::integer($nestingLevel, 'Make static analysis happy');

        return $cleaned . str_repeat($pdf, $nestingLevel);
    }
}
