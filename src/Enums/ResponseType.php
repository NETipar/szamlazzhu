<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Enums;

enum ResponseType: int
{
    case Text = 1;
    case Xml = 2;
    case TaxPayerXml = 3;
}
