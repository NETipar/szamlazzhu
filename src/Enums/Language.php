<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Enums;

enum Language: string
{
    case Hungarian = 'hu';
    case English = 'en';
    case German = 'de';
    case Italian = 'it';
    case Romanian = 'ro';
    case Slovak = 'sk';
    case Croatian = 'hr';
    case French = 'fr';
    case Spanish = 'es';
    case Czech = 'cz';
    case Polish = 'pl';

    public function label(): string
    {
        return match ($this) {
            self::Hungarian => 'magyar',
            self::English => 'angol',
            self::German => 'német',
            self::Italian => 'olasz',
            self::Romanian => 'román',
            self::Slovak => 'szlovák',
            self::Croatian => 'horvát',
            self::French => 'francia',
            self::Spanish => 'spanyol',
            self::Czech => 'cseh',
            self::Polish => 'lengyel',
        };
    }
}
