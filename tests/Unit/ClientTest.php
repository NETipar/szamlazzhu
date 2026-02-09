<?php

use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Enums\ResponseType;
use NETipar\Szamlazzhu\Session\SessionManager;

function createClient(array $configOverrides = []): Client
{
    $config = array_merge([
        'api_key' => 'test-api-key',
        'api_url' => 'https://www.szamlazz.hu/szamla/',
        'download_pdf' => true,
        'response_type' => 1,
        'timeout' => 30,
        'connect_timeout' => 0,
        'aggregator' => '',
        'session' => [
            'driver' => 'null',
            'ttl' => 3600,
        ],
    ], $configOverrides);

    $sessionManager = new SessionManager($config);

    return new Client($config, $sessionManager);
}

it('returns the api key', function () {
    $client = createClient(['api_key' => 'my-key-123']);

    expect($client->getApiKey())->toBe('my-key-123');
});

it('returns the api url', function () {
    $client = createClient(['api_url' => 'https://custom.url/']);

    expect($client->getApiUrl())->toBe('https://custom.url/');
});

it('returns default api url when not configured', function () {
    $client = createClient();

    expect($client->getApiUrl())->toBe('https://www.szamlazz.hu/szamla/');
});

it('returns download pdf flag', function () {
    $client = createClient(['download_pdf' => false]);

    expect($client->isDownloadPdf())->toBeFalse();
});

it('returns response type', function () {
    $client = createClient(['response_type' => 2]);

    expect($client->getResponseType())->toBe(ResponseType::Xml);
});

it('returns timeout', function () {
    $client = createClient(['timeout' => 60]);

    expect($client->getTimeout())->toBe(60);
});

it('returns connect timeout', function () {
    $client = createClient(['connect_timeout' => 10]);

    expect($client->getConnectTimeout())->toBe(10);
});

it('returns aggregator', function () {
    $client = createClient(['aggregator' => 'my-aggregator']);

    expect($client->getAggregator())->toBe('my-aggregator');
});

it('manages custom headers', function () {
    $client = createClient();

    $client->addCustomHeader('X-Custom', 'value');

    expect($client->getCustomHeaders())->toBe(['X-Custom' => 'value']);

    $client->removeCustomHeader('X-Custom');

    expect($client->getCustomHeaders())->toBe([]);
});

it('supports fluent custom header api', function () {
    $client = createClient();

    $result = $client->addCustomHeader('X-Test', 'val');

    expect($result)->toBe($client);
});
