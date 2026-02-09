<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Enums;

enum WaybillType: string
{
    case Transoflex = 'Transoflex';
    case Sprinter = 'Sprinter';
    case Ppp = 'PPP';
    case Mpl = 'MPL';
}
