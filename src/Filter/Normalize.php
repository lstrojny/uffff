<?php

declare(strict_types=1);

namespace Uffff\Filter;

use Normalizer;
use Uffff\Contracts\Filter;
use Uffff\Value\NormalizationForm;
use Webmozart\Assert\Assert;

/**
 * @psalm-immutable
 */
readonly final class Normalize implements Filter
{
    public function __construct(
        private NormalizationForm $form = NormalizationForm::NFC
    ) {
    }

    public function __invoke(string $value): string
    {
        $form = match ($this->form) {
            NormalizationForm::NFC => Normalizer::NFC,
            NormalizationForm::NFD => Normalizer::NFD,
        };

        if (Normalizer::isNormalized($value, $form)) {
            return $value;
        }

        $normalized = Normalizer::normalize($value, $form);

        Assert::string($normalized, 'Value "%s" cannot be normalized');

        return $normalized;
    }
}
