<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NETipar\Szamlazzhu\Client;

function fakeInvoiceDataResponse(): void
{
    $invoiceXml = '<?xml version="1.0" encoding="UTF-8"?>'
        .'<xmlszamlavalasz>'
        .'<alap><szamlaszam>E-TESZT-2026-001</szamlaszam><tipus>SZ</tipus></alap>'
        .'</xmlszamlavalasz>';

    Http::fake([
        'www.szamlazz.hu/*' => Http::response($invoiceXml, 200, [
            'Content-Type' => 'application/octet-stream',
            'szlahu_szamlaszam' => 'E-TESZT-2026-001',
            'szlahu_id' => '123456789',
        ]),
    ]);
}

it('sends custom headers with the request', function () {
    fakeInvoiceDataResponse();

    $client = app(Client::class);
    $client->addCustomHeader('X-Custom', 'abc');
    $client->getInvoiceData('E-TESZT-2026-001');

    Http::assertSent(function ($request) {
        return $request->hasHeader('X-Custom', 'abc');
    });
});

it('does not send removed custom headers', function () {
    fakeInvoiceDataResponse();

    $client = app(Client::class);
    $client->addCustomHeader('X-Custom', 'abc');
    $client->removeCustomHeader('X-Custom');
    $client->getInvoiceData('E-TESZT-2026-001');

    Http::assertSent(function ($request) {
        return ! $request->hasHeader('X-Custom');
    });
});
