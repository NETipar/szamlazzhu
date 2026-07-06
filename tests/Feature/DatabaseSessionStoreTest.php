<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use NETipar\Szamlazzhu\Session\Drivers\DatabaseSessionStore;

beforeEach(function () {
    // Testbench 8 (Laravel 10) defaults to the mysql connection; force the in-memory sqlite one.
    config(['database.default' => 'testing']);

    $migration = require dirname(__DIR__, 2).'/database/migrations/create_szamlazzhu_sessions_table.php.stub';
    $migration->up();
});

it('stores and retrieves values from the database', function () {
    $store = new DatabaseSessionStore;

    $store->put('session-key', 'session-value', 3600);

    expect($store->get('session-key'))->toBe('session-value')
        ->and($store->has('session-key'))->toBeTrue();

    $store->forget('session-key');

    expect($store->get('session-key'))->toBeNull()
        ->and($store->has('session-key'))->toBeFalse();
});

it('does not return expired values', function () {
    $store = new DatabaseSessionStore;

    $store->put('expired-key', 'value', 3600);

    DB::table('szamlazzhu_sessions')
        ->where('key', 'expired-key')
        ->update(['expires_at' => now()->subMinute()]);

    expect($store->get('expired-key'))->toBeNull()
        ->and($store->has('expired-key'))->toBeFalse();
});

it('updates existing keys on repeated put', function () {
    $store = new DatabaseSessionStore;

    $store->put('session-key', 'first', 3600);
    $store->put('session-key', 'second', 3600);

    expect($store->get('session-key'))->toBe('second')
        ->and(DB::table('szamlazzhu_sessions')->count())->toBe(1);
});
