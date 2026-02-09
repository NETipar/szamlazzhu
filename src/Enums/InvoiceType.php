<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Enums;

enum InvoiceType: int
{
    case Paper = 1;
    case Electronic = 2;
}
