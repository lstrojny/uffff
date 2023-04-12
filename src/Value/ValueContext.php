<?php

declare(strict_types=1);

namespace Uffff\Value;

/**
 * @internal
 * @psalm-immutable
 */
enum ValueContext
{
    case INPUT;
    case OUTPUT;
}
