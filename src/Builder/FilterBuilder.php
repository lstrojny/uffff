<?php

declare(strict_types=1);

namespace Uffff\Builder;

use Uffff\Contracts\Filter;
use Uffff\Filter\CheckIfUnicode;
use Uffff\Filter\CloseBidirectionalMarker;
use Uffff\Filter\HarmonizeNewlines;
use Uffff\Filter\NormalizeForm;
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
            fn (callable $filter): callable =>
            fn (string $value): string => $value === '' ? $value : $filter($value);

        $filters = array_map(
            $shortCircuitEmpty,
            [
                FlyweightFactory::create(CheckIfUnicode::class),
                FlyweightFactory::createWith(
                    NormalizeForm::class,
                    [$this->normalizationForm],
                    $this->normalizationForm->name
                ),
                FlyweightFactory::createWith(HarmonizeNewlines::class, [$this->newline], $this->newline->name),
                ...($this->trimWhitespace ? [FlyweightFactory::create(TrimWhitespace::class)] : []),
                FlyweightFactory::create(CloseBidirectionalMarker::class),
                ...$this->filters,
            ]
        );

        return $shortCircuitEmpty(
            fn (string $value): string => array_reduce(
                $filters,
                /**
                 * @param FilterFn $filter
                 */
                fn (string $value, callable $filter): string => $filter($value),
                $value
            )
        );
    }
}
