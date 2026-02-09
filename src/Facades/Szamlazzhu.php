<?php

namespace NETipar\Szamlazzhu\Facades;

use Illuminate\Support\Facades\Facade;

class Szamlazzhu extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \NETipar\Szamlazzhu\Client::class;
    }
}
