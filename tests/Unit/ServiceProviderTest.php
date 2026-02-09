<?php

use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Session\Contracts\SessionStore;
use NETipar\Szamlazzhu\Session\Drivers\CacheSessionStore;
use NETipar\Szamlazzhu\Session\SessionManager;

it('registers the config', function () {
    $config = config('szamlazzhu');

    expect($config)->toBeArray()
        ->and($config['api_url'])->toBe('https://www.szamlazz.hu/szamla/')
        ->and($config['timeout'])->toBe(30)
        ->and($config['download_pdf'])->toBeTrue()
        ->and($config['response_type'])->toBe(1);
});

it('registers SessionStore singleton', function () {
    $store = app(SessionStore::class);

    expect($store)->toBeInstanceOf(CacheSessionStore::class);
});

it('registers SessionManager singleton', function () {
    $manager = app(SessionManager::class);

    expect($manager)->toBeInstanceOf(SessionManager::class);
});

it('registers Client singleton', function () {
    config(['szamlazzhu.api_key' => 'test-key']);

    $client = app(Client::class);

    expect($client)->toBeInstanceOf(Client::class)
        ->and($client->getApiKey())->toBe('test-key');
});

it('resolves the same Client instance', function () {
    $client1 = app(Client::class);
    $client2 = app(Client::class);

    expect($client1)->toBe($client2);
});
