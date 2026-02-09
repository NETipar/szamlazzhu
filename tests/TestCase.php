<?php

namespace NETipar\Szamlazzhu\Tests;

use NETipar\Szamlazzhu\SzamlazzhuServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
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
            'Szamlazzhu' => \NETipar\Szamlazzhu\Facades\Szamlazzhu::class,
        ];
    }
}
