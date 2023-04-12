<?php

declare(strict_types=1);

namespace Uffff\Filter;

use Uffff\Contract\Filter;
use Uffff\Value\Newline;
use Webmozart\Assert\Assert;

/**
 * @psalm-immutable
 * @internal
 */
final readonly class HarmonizeNewlines implements Filter
{
    // Order matters, Windows must come first
    private const ANY_NEWLINE = '/(?:' . Newline::WINDOWS->value . '|' . Newline::MAC->value . '|' . Newline::UNIX->value . ')/';

    public function __construct(
        private Newline $newline
    ) {
    }

    public function __invoke(string $text): string
    {
        $harmonized = preg_replace(self::ANY_NEWLINE, $this->newline->value, $text);

        Assert::string($harmonized, 'Cannot standardize newlines in "%s"');

        return $harmonized;
    }
}
