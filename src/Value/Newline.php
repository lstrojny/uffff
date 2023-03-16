<?php

declare(strict_types=1);

namespace Uffff\Value;

enum Newline: string
{
    case UNIX = "\n";
    case WINDOWS = "\r\n";
    case MAC = "\r";
}
