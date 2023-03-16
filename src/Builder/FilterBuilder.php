<?php

declare(strict_types=1);

namespace Uffff\Builder;

use Uffff\Contract\Filter;
use Uffff\Filter\BalanceBidirectionalMarker;
use Uffff\Filter\CheckIfUnicode;
use Uffff\Filter\HarmonizeNewlines;
use Uffff\Filter\NormalizeForm;
use Uffff\Filter\StripNullByte;
use Uffff\Filter\TrimWhitespace;
use Uffff\Value\Newline;
use Uffff\Value\NormalizationForm;

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
     * @return FilterFn
     */
    public function build(): callable
    {
        $shortCircuitEmpty =
            /**
             * @param FilterFn $filter
             * @return FilterFn
             */
            static fn (callable $filter): callable =>
            static fn (string $value): string => $value === '' ? $value : $filter($value);

        $filters = array_map(
            $shortCircuitEmpty,
            [
                FlyweightFactory::create(StripNullByte::class),
                FlyweightFactory::create(CheckIfUnicode::class),
                FlyweightFactory::createWith(
                    NormalizeForm::class,
                    [$this->normalizationForm],
                    $this->normalizationForm->name
                ),
                FlyweightFactory::createWith(HarmonizeNewlines::class, [$this->newline], $this->newline->name),
                ...($this->trimWhitespace ? [FlyweightFactory::create(TrimWhitespace::class)] : []),
                FlyweightFactory::create(BalanceBidirectionalMarker::class),
                ...$this->filters,
                FlyweightFactory::create(CheckIfUnicode::class),
            ]
        );

        return $shortCircuitEmpty(
            static fn (string $value): string => array_reduce(
                $filters,
                /**
                 * @param FilterFn $filter
                 */
                static fn (string $value, callable $filter): string => $filter($value),
                $value
            )
        );
    }
}
