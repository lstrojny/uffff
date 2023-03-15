<?php

declare(strict_types=1);

namespace Uffff\Filter;

use Assert\AssertionFailedException;
use Normalizer;
use Uffff\Assertion;
use Uffff\Contracts\Filter;
use Uffff\Value\NormalizationForm;

/**
 * @psalm-immutable
 */
readonly final class Normalize implements Filter
{
    public function __construct(
        private NormalizationForm $form = NormalizationForm::NFC
    ) {
    }

    /**
     * @throws AssertionFailedException
     */
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

        Assertion::string($normalized, sprintf('Value "%s" cannot be normalized', $value));

        return $normalized;
    }
}
