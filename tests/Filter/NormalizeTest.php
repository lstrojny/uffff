<?php

declare(strict_types=1);

namespace Uffff\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Uffff\Filter\Normalize;
use Uffff\Value\NormalizationForm;

final class NormalizeTest extends TestCase
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
     * @covers \Uffff\Filter\Normalize::__invoke
     */
    public function testNfcNormalization(string $input, string $output): void
    {
        $normalize = new Normalize();

        self::assertSame($output, $normalize($input));
    }

    /**
     * @dataProvider nfc
     * @covers \Uffff\Filter\Normalize::__invoke
     */
    public function testNfdNormalization(string $output, string $input): void
    {
        $normalize = new Normalize(NormalizationForm::NFD);

        self::assertSame($output, $normalize($input));
    }
}
