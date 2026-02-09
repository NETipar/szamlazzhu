<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Enums;

enum LookupType: int
{
    case InvoiceNumber = 1;
    case OrderNumber = 2;
    case ExternalId = 3;
}
