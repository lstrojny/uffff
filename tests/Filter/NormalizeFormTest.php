<?php

declare(strict_types=1);

namespace Uffff\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Uffff\Filter\NormalizeForm;
use Uffff\Value\NormalizationForm;

/**
 * @covers \Uffff\Filter\NormalizeForm
 */
final class NormalizeFormTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function nfc(): array
    {
        return [
            'empty value' => ['', ''],
            'ASCII value' => ['value', 'value'],
            'Latin a umlaut' => ["\u{0061}\u{0308}", "\u{00E4}"],
        ];
    }

    /**
     * @dataProvider nfc
     */
    public function testNfcNormalization(string $input, string $output): void
    {
        $normalize = new NormalizeForm(NormalizationForm::NFC);

        self::assertSame($output, $normalize($input));
    }

    /**
     * @dataProvider nfc
     */
    public function testNfdNormalization(string $output, string $input): void
    {
        $normalize = new NormalizeForm(NormalizationForm::NFD);

        self::assertSame($output, $normalize($input));
    }
}
