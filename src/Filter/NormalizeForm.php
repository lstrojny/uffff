<?php

declare(strict_types=1);

namespace Uffff\Filter;

use Normalizer;
use Uffff\Contract\Filter;
use Uffff\Value\NormalizationForm;
use Webmozart\Assert\Assert;

/**
 * @psalm-immutable
 * @internal
 */
readonly final class NormalizeForm implements Filter
{
    public function __construct(
        private NormalizationForm $form
    ) {
    }

    /**
     * @phpstan-pure
     */
    public function __invoke(string $text): string
    {
        $form = match ($this->form) {
            NormalizationForm::NFC => Normalizer::NFC,
            NormalizationForm::NFD => Normalizer::NFD,
        };

        if (Normalizer::isNormalized($text, $form)) {
            return $text;
        }

        $normalized = Normalizer::normalize($text, $form);

        Assert::string($normalized, sprintf('Value "%s" cannot be normalized', $text));

        return $normalized;
    }
}
