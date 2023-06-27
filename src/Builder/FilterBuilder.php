<?php

namespace Uffff\Builder;

class FilterBuilder
{
    /**
     * @return callable(string):string
     */
    public function build(): callable
    {
        return static fn(string $v) => $v;
    }
}

enum Newline: string
{
    case UNIX = "\n";
    case WINDOWS = "\r\n";
    case MAC = "\r";
}

class HarmonizeNewlines
{
    // Order matters, Windows must come first
    private const ANY_NEWLINE = '/(?:' . Newline::WINDOWS->value . '|' . Newline::MAC->value . '|' . Newline::UNIX->value . ')/';

    public function __invoke(string $text): string
    {
        $harmonized = preg_replace(self::ANY_NEWLINE, Newline::UNIX->value, $text);

        assert(is_string($harmonized));

        return $harmonized;
    }
}