<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Enums;

enum InvoiceTemplate: string
{
    case Default = 'SzlaMost';
    case Traditional = 'SzlaNoEnv';
    case EnvFriendly = 'SzlaAlap';
    case Thermal8cm = 'Szla8cm';
    case Retro = 'SzlaTomb';
}
