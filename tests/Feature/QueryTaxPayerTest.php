<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NETipar\Szamlazzhu\Client;

it('queries taxpayer data successfully', function () {
    $taxPayerXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        .'<QueryTaxpayerResponse>'
        .'<header><requestId>131450_test123</requestId>'
        .'<timestamp>2026-02-09T23:13:59.570Z</timestamp>'
        .'<requestVersion>3.0</requestVersion>'
        .'<headerVersion>1.0</headerVersion></header>'
        .'<result><funcCode>OK</funcCode></result>'
        .'<taxpayerValidity>false</taxpayerValidity>'
        .'</QueryTaxpayerResponse>';

    Http::fake([
        'www.szamlazz.hu/*' => Http::response($taxPayerXml, 200, [
            'Content-Type' => 'application/xml',
        ]),
    ]);

    $client = app(Client::class);
    $result = $client->getTaxPayer('12345678');

    expect($result->isSuccess())->toBeTrue();

    $data = $result->toArray();

    expect($data['result']['result']['funcCode'])->toBe('OK');
});
