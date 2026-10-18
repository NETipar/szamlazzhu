<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Tests;

use NETipar\Szamlazzhu\Facades\Szamlazzhu;
use NETipar\Szamlazzhu\SzamlazzhuServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            SzamlazzhuServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Szamlazzhu' => Szamlazzhu::class,
        ];
    }
}
