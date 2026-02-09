<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Document\ReverseInvoice;

it('generates a reverse invoice successfully', function () {
    Http::fake([
        'www.szamlazz.hu/*' => Http::response(
            base64_decode('JVBERi0xLjQKMSAwIG9iago8PAovVGl0bGUgKP7/AFQAZQBzAHoAdCkKPj4KZW5kb2JqCg=='),
            200,
            [
                'szlahu_szamlaszam' => 'E-TESZT-2026-002',
                'szlahu_id' => '123456790',
                'szlahu_nettovegosszeg' => '-20000',
                'szlahu_bruttovegosszeg' => '-25400',
                'szlahu_kintlevoseg' => '0',
                'Content-Type' => 'application/pdf',
            ]
        ),
    ]);

    $client = app(Client::class);

    $invoice = new ReverseInvoice;
    $invoice->getHeader()->setInvoiceNumber('E-TESZT-2026-001');

    $result = $client->generateReverseInvoice($invoice);

    expect($result->isSuccess())->toBeTrue()
        ->and($result->getDocumentNumber())->toBe('E-TESZT-2026-002')
        ->and($result->getPdfFile())->not->toBeNull();
});

it('builds correct reverse invoice xml', function () {
    $invoice = new ReverseInvoice;
    $invoice->getHeader()->setInvoiceNumber('E-TESZT-2026-001');
    $invoice->getHeader()->setIssueDate('2026-02-09');

    $settings = ['szamlaagentkulcs' => 'test-key'];
    $xmlArray = $invoice->toXmlArray($settings);

    expect($xmlArray)->toHaveKey('beallitasok')
        ->and($xmlArray)->toHaveKey('fejlec')
        ->and($xmlArray['fejlec']['szamlaszam'])->toBe('E-TESZT-2026-001');
});
