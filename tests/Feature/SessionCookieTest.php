<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Session\SessionManager;

beforeEach(function () {
    config(['szamlazzhu.api_key' => 'test-agent-key']);

    Http::fake([
        'www.szamlazz.hu/*' => Http::response(
            '<?xml version="1.0" encoding="UTF-8"?><xmlszamlavalasz><alap><szamlaszam>E-TESZT-2026-001</szamlaszam></alap></xmlszamlavalasz>',
            200,
            [
                'szlahu_szamlaszam' => 'E-TESZT-2026-001',
                'Set-Cookie' => 'JSESSIONID=session-from-api; Path=/; HttpOnly',
            ],
        ),
    ]);
});

it('stores the szamlazz.hu session under the sha256 hash of the api key', function () {
    app(Client::class)->getInvoiceData('E-TESZT-2026-001');

    expect(app(SessionManager::class)->getSessionId(hash('sha256', 'test-agent-key')))->toBe('session-from-api');
});

it('sends the stored session cookie with the next request', function () {
    app(SessionManager::class)->setSessionId(hash('sha256', 'test-agent-key'), 'stored-session');

    app(Client::class)->getInvoiceData('E-TESZT-2026-001');

    Http::assertSent(function (Request $request) {
        return str_contains($request->toPsrRequest()->getHeaderLine('Cookie'), 'JSESSIONID=stored-session');
    });
});
