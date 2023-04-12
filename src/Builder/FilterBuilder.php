<?php

declare(strict_types=1);

namespace Uffff\Builder;

use Uffff\Contract\Filter;
use Uffff\Filter\AssertWellFormedUnicode;
use Uffff\Filter\BalanceBidirectionalMarker;
use Uffff\Filter\HarmonizeNewlines;
use Uffff\Filter\NormalizeForm;
use Uffff\Filter\StripNullByte;
use Uffff\Filter\TrimWhitespace;
use Uffff\Value\Newline;
use Uffff\Value\NormalizationForm;
use Uffff\Value\ValueContext;

/**
 * @phpstan-import-type FilterFn from Filter
 */
final class FilterBuilder
{
    private NormalizationForm $normalizationForm = NormalizationForm::NFC;

    private Newline $newline = Newline::UNIX;

    private bool $trimWhitespace = true;

    /**
     * @var list<FilterFn>
     */
    private array $filters = [];

    public function normalizeForm(NormalizationForm $normalizationForm): self
    {
        $this->normalizationForm = $normalizationForm;

        return $this;
    }

    public function harmonizeNewlines(Newline $newline): self
    {
        $this->newline = $newline;

        return $this;
    }

    public function trimWhitespace(bool $trim): self
    {
        $this->trimWhitespace = $trim;

        return $this;
    }

    /**
     * @param FilterFn $filter
     */
    public function add(callable $filter): self
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * @psalm-mutation-free
     * @return FilterFn
     */
    public function build(): callable
    {
        $shortCircuitEmpty =
            /**
             * @param FilterFn $filter
             * @return FilterFn
             */
            static function (callable $filter): callable {
                return static function (string $text) use ($filter): string {
                    /** @var FilterFn $filter */
                    return $text === '' ? $text : $filter($text);
                };
            };

        $filters = array_map(
            $shortCircuitEmpty,
            [
                FilterFactory::create(StripNullByte::class),
                FilterFactory::createWith(
                    AssertWellFormedUnicode::class,
                    [ValueContext::INPUT],
                    ValueContext::INPUT->name
                ),
                FilterFactory::createWith(
                    NormalizeForm::class,
                    [$this->normalizationForm],
                    $this->normalizationForm->name
                ),
                FilterFactory::createWith(HarmonizeNewlines::class, [$this->newline], $this->newline->name),
                ...$this->trimWhitespace ? [FilterFactory::create(TrimWhitespace::class)] : [],
                FilterFactory::create(BalanceBidirectionalMarker::class),
                ...$this->filters,
                FilterFactory::createWith(
                    AssertWellFormedUnicode::class,
                    [ValueContext::OUTPUT],
                    ValueContext::OUTPUT->name
                ),
            ]
        );

        return $shortCircuitEmpty(
            static fn (string $text): string => array_reduce(
                $filters,
                /**
                 * @param FilterFn $filter
                 */
                static fn (string $text, callable $filter): string => $filter($text),
                $text
            )
        );
    }
}
