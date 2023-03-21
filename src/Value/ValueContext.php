<?php

declare(strict_types=1);

namespace Uffff\Value;

/**
 * @psalm-immutable
 * @internal
 */
enum ValueContext
{
    case INPUT;
    case OUTPUT;
}
