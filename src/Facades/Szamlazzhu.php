<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Facades;

use Illuminate\Support\Facades\Facade;
use NETipar\Szamlazzhu\Client;

class Szamlazzhu extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Client::class;
    }
}
