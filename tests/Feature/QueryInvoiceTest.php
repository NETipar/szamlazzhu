<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NETipar\Szamlazzhu\Client;

it('queries invoice data by invoice number', function () {
    $invoiceXml = '<?xml version="1.0" encoding="UTF-8"?>'
        .'<xmlszamlavalasz>'
        .'<szallito><nev>Teszt Eladó Kft.</nev></szallito>'
        .'<alap><szamlaszam>E-TESZT-2026-001</szamlaszam><tipus>SZ</tipus>'
        .'<kelt>2026-02-09</kelt><telj>2026-02-09</telj><fizh>2026-02-17</fizh>'
        .'<fizmod>átutalás</fizmod><penzforg>false</penzforg></alap>'
        .'<vevo><nev>Teszt Kft.</nev><adoszam>12345678-1-42</adoszam></vevo>'
        .'<tetelek><tetel><nev>Teszt termék</nev><mennyiseg>2</mennyiseg>'
        .'<nettoegysegar>10000</nettoegysegar><netto>20000</netto>'
        .'<afa>5400</afa><brutto>25400</brutto></tetel></tetelek>'
        .'<osszegek><totalossz><netto>20000</netto><afa>5400</afa><brutto>25400</brutto></totalossz></osszegek>'
        .'</xmlszamlavalasz>';

    Http::fake([
        'www.szamlazz.hu/*' => Http::response($invoiceXml, 200, [
            'Content-Type' => 'application/octet-stream',
            'szlahu_szamlaszam' => 'E-TESZT-2026-001',
            'szlahu_id' => '123456789',
            'szlahu_nettovegosszeg' => '20000',
            'szlahu_bruttovegosszeg' => '25400',
            'szlahu_kintlevoseg' => '0',
        ]),
    ]);

    $client = app(Client::class);
    $result = $client->getInvoiceData('E-TESZT-2026-001');

    expect($result->isSuccess())->toBeTrue()
        ->and($result->getDocumentNumber())->toBe('E-TESZT-2026-001');

    $data = $result->toArray();

    expect($data['result']['alap']['szamlaszam'])->toBe('E-TESZT-2026-001')
        ->and($data['result']['vevo']['nev'])->toBe('Teszt Kft.');
});

it('downloads invoice pdf', function () {
    $pdfContent = '%PDF-1.4 fake pdf content for testing';

    Http::fake([
        'www.szamlazz.hu/*' => Http::response($pdfContent, 200, [
            'szlahu_szamlaszam' => 'E-TESZT-2026-001',
            'szlahu_id' => '123456789',
            'szlahu_nettovegosszeg' => '20000',
            'szlahu_bruttovegosszeg' => '25400',
            'szlahu_kintlevoseg' => '0',
            'Content-Type' => 'application/pdf',
        ]),
    ]);

    $client = app(Client::class);
    $result = $client->getInvoicePdf('E-TESZT-2026-001');

    expect($result->isSuccess())->toBeTrue()
        ->and($result->getPdfFile())->not->toBeNull()
        ->and($result->getDocumentNumber())->toBe('E-TESZT-2026-001');
});
