<?php

declare(strict_types=1);

namespace Uffff\Builder;

use Uffff\Contracts\Filter;
use Uffff\Filter\CheckIfUnicode;
use Uffff\Filter\CloseBidirectionalMarker;
use Uffff\Filter\Normalize;
use Uffff\Filter\TrimWhitespace;
use Uffff\Value\NormalizationForm;

/**
 * @phpstan-import-type FilterFn from Filter
 */
final class FilterBuilder
{
    private NormalizationForm $normalizationForm = NormalizationForm::NFC;

    private bool $trimWhitespace = true;

    /**
     * @var list<FilterFn>
     */
    private array $filters = [];

    public function normalize(NormalizationForm $normalizationForm): self
    {
        $this->normalizationForm = $normalizationForm;

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
        $shortCircuitEmpty = fn (callable $filter): callable =>
            fn (string $value): string => $value === '' ? $value : $filter($value);

        $filters = array_map(
            $shortCircuitEmpty,
            [
                FlyweightFactory::create(CheckIfUnicode::class),
                FlyweightFactory::createWith(
                    Normalize::class,
                    [$this->normalizationForm],
                    $this->normalizationForm->name
                ),
                ...($this->trimWhitespace ? [FlyweightFactory::create(TrimWhitespace::class)] : []),
                FlyweightFactory::create(CloseBidirectionalMarker::class),
                ...$this->filters,
            ]
        );

        return $shortCircuitEmpty(
            fn (string $value): string => array_reduce(
                $filters,
                fn (string $value, callable $filter): string => $filter($value),
                $value
            )
        );
    }
}
