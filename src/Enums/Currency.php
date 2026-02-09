<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Enums;

enum Currency: string
{
    case Ft = 'Ft';
    case HUF = 'HUF';
    case EUR = 'EUR';
    case CHF = 'CHF';
    case USD = 'USD';
    case AED = 'AED';
    case AUD = 'AUD';
    case BGN = 'BGN';
    case BRL = 'BRL';
    case CAD = 'CAD';
    case CNY = 'CNY';
    case CZK = 'CZK';
    case DKK = 'DKK';
    case EEK = 'EEK';
    case GBP = 'GBP';
    case HKD = 'HKD';
    case HRK = 'HRK';
    case IDR = 'IDR';
    case ILS = 'ILS';
    case INR = 'INR';
    case ISK = 'ISK';
    case JPY = 'JPY';
    case KRW = 'KRW';
    case LTL = 'LTL';
    case LVL = 'LVL';
    case MXN = 'MXN';
    case MYR = 'MYR';
    case NOK = 'NOK';
    case NZD = 'NZD';
    case PHP = 'PHP';
    case PLN = 'PLN';
    case RON = 'RON';
    case RSD = 'RSD';
    case RUB = 'RUB';
    case SEK = 'SEK';
    case SGD = 'SGD';
    case THB = 'THB';
    case TRY = 'TRY';
    case UAH = 'UAH';
    case VND = 'VND';
    case ZAR = 'ZAR';

    public function label(): string
    {
        return match ($this) {
            self::Ft, self::HUF => 'forint',
            self::EUR => 'euró',
            self::CHF => 'svájci frank',
            self::USD => 'amerikai dollár',
            self::AED => 'Arab Emírségek dirham',
            self::AUD => 'ausztrál dollár',
            self::BGN => 'bolgár leva',
            self::BRL => 'brazil real',
            self::CAD => 'kanadai dollár',
            self::CNY => 'kínai jüan',
            self::CZK => 'cseh korona',
            self::DKK => 'dán korona',
            self::EEK => 'észt korona',
            self::GBP => 'angol font',
            self::HKD => 'hongkongi dollár',
            self::HRK => 'horvát kúna',
            self::IDR => 'indonéz rúpia',
            self::ILS => 'izraeli sékel',
            self::INR => 'indiai rúpia',
            self::ISK => 'izlandi korona',
            self::JPY => 'japán jen',
            self::KRW => 'dél-koreai won',
            self::LTL => 'litván litas',
            self::LVL => 'lett lat',
            self::MXN => 'mexikói peso',
            self::MYR => 'maláj ringgit',
            self::NOK => 'norvég korona',
            self::NZD => 'új-zélandi dollár',
            self::PHP => 'fülöp-szigeteki peso',
            self::PLN => 'lengyel zloty',
            self::RON => 'új román lej',
            self::RSD => 'szerb dínár',
            self::RUB => 'orosz rubel',
            self::SEK => 'svéd korona',
            self::SGD => 'szingapúri dollár',
            self::THB => 'thai bát',
            self::TRY => 'török líra',
            self::UAH => 'ukrán hryvna',
            self::VND => 'vietnámi dong',
            self::ZAR => 'dél-afrikai rand',
        };
    }
}
