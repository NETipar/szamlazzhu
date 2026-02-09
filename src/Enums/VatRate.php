<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Enums;

enum VatRate: string
{
    case Percent27 = '27';
    case Percent25 = '25';
    case Percent20 = '20';
    case Percent19 = '19';
    case Percent18 = '18';
    case Percent7 = '7';
    case Percent5 = '5';
    case Percent0 = '0';
    case TAM = 'TAM';
    case AAM = 'AAM';
    case EU = 'EU';
    case EUK = 'EUK';
    case MAA = 'MAA';
    case FAFA = 'F.AFA';
    case KAFA = 'K.AFA';
    case AKK = 'ÁKK';
    case TAHK = 'TAHK';
    case EUT = 'EUT';
    case EUKT = 'EUKT';
    case KBAET = 'KBAET';
    case KBAUK = 'KBAUK';
    case EAM = 'EAM';
    case ATK = 'ATK';
    case EUFAD37 = 'EUFAD37';
    case EUFADE = 'EUFADE';
    case EUE = 'EUE';
    case HO = 'HO';
}
