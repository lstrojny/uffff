<?php

declare(strict_types=1);

namespace Uffff\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Uffff\Filter\HarmonizeNewlines;
use Uffff\Value\Newline;

/**
 * @covers \Uffff\Filter\HarmonizeNewlines
 */
final class HarmonizeNewlinesTest extends TestCase
{
    /**
     * @return array<string, array{Newline, string, string}>
     */
    public static function newlines(): array
    {
        $cases = [
            'empty string' => ['', [[Newline::UNIX, ''], [Newline::WINDOWS, ''], [Newline::MAC, '']]],
            'new newline' => [
                'foobarbaz something',
                [
                    [Newline::UNIX, 'foobarbaz something'],
                    [Newline::WINDOWS, 'foobarbaz something'],
                    [Newline::MAC, 'foobarbaz something'],
                ],
            ],
            'UNIX newlines' => [
                "foo\n\nbar\nbaz",
                [
                    [Newline::UNIX, "foo\n\nbar\nbaz"],
                    [Newline::WINDOWS, "foo\r\n\r\nbar\r\nbaz"],
                    [Newline::MAC, "foo\r\rbar\rbaz"],
                ],
            ],
            'Windows newlines' => [
                "foo\r\n\r\nbar\r\nbaz",
                [
                    [Newline::UNIX, "foo\n\nbar\nbaz"],
                    [Newline::WINDOWS, "foo\r\n\r\nbar\r\nbaz"],
                    [Newline::MAC, "foo\r\rbar\rbaz"],
                ],
            ],
            'Mac newlines' => [
                "foo\rbar\r\rbaz",
                [
                    [Newline::UNIX, "foo\nbar\n\nbaz"],
                    [Newline::WINDOWS, "foo\r\nbar\r\n\r\nbaz"],
                    [Newline::MAC, "foo\rbar\r\rbaz"],
                ],
            ],
            'Mixed newlines' => [
                "\r\nfoo\r\n\rbar\rbaz\n",
                [
                    [Newline::UNIX, "\nfoo\n\nbar\nbaz\n"],
                    [Newline::WINDOWS, "\r\nfoo\r\n\r\nbar\r\nbaz\r\n"],
                    [Newline::MAC, "\rfoo\r\rbar\rbaz\r"],
                ],
            ],
        ];

        $data = [];

        foreach ($cases as $name => [$input, $outputs]) {
            foreach ($outputs as [$newline, $output]) {
                $data[sprintf('%s to %s', $name, $newline->name)] = [$newline, $input, $output];
            }
        }

        return $data;
    }

    /**
     * @dataProvider newlines
     */
    public function testHarmonizeNewlines(Newline $newline, string $input, string $output): void
    {
        $standardize = new HarmonizeNewlines($newline);

        self::assertSame($output, $standardize($input));
    }
}
