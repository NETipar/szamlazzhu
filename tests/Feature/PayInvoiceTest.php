<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\CreditNote\InvoiceCreditNote;
use NETipar\Szamlazzhu\Document\Invoice;

it('records a payment on an invoice successfully', function () {
    Http::fake([
        'www.szamlazz.hu/*' => Http::response('<?xml version="1.0" encoding="UTF-8"?><response>OK</response>', 200, [
            'szlahu_szamlaszam' => 'E-TESZT-2026-001',
            'szlahu_id' => '123456789',
            'szlahu_nettovegosszeg' => '20000',
            'szlahu_bruttovegosszeg' => '25400',
            'szlahu_kintlevoseg' => '0',
            'Content-Type' => 'text/plain',
        ]),
    ]);

    $client = app(Client::class);

    $invoice = new Invoice;
    $invoice->getHeader()->setInvoiceNumber('E-TESZT-2026-001');

    $creditNote = new InvoiceCreditNote(
        date: '2026-02-09',
        amount: 25400.0,
    );
    $invoice->addCreditNote($creditNote);

    $result = $client->payInvoice($invoice);

    expect($result->isSuccess())->toBeTrue()
        ->and($result->getDocumentNumber())->toBe('E-TESZT-2026-001');
});

it('builds correct credit note xml', function () {
    $invoice = new Invoice;
    $invoice->getHeader()->setInvoiceNumber('E-TESZT-2026-001');

    $creditNote = new InvoiceCreditNote(
        date: '2026-02-09',
        amount: 25400.0,
    );
    $invoice->addCreditNote($creditNote);

    $settings = [
        'szamlaagentkulcs' => 'test-key',
        'szamlaszam' => 'E-TESZT-2026-001',
        'additiv' => true,
        'aggregator' => '',
        'valaszVerzio' => 1,
    ];

    $xmlArray = $invoice->toCreditsXmlArray($settings);

    expect($xmlArray)->toHaveKey('beallitasok')
        ->and($xmlArray)->toHaveKey('note0')
        ->and($xmlArray['note0']['datum'])->toBe('2026-02-09')
        ->and($xmlArray['note0']['osszeg'])->toBe('25400.00')
        ->and($xmlArray['note0']['jogcim'])->toBe('átutalás');
});
