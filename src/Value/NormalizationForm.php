<?php

declare(strict_types=1);

namespace Uffff\Value;

/**
 * @psalm-immutable
 */
enum NormalizationForm
{
    case NFC;
    case NFD;
}
