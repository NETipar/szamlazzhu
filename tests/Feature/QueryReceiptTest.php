<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NETipar\Szamlazzhu\Client;

it('queries receipt data by receipt number', function () {
    $receiptXml = '<?xml version="1.0" encoding="UTF-8"?>'
        .'<xmlnyugtavalasz>'
        .'<sikeres>true</sikeres>'
        .'<hibakod></hibakod>'
        .'<hibauzenet></hibauzenet>'
        .'<nyugta>'
        .'<alap><id>100</id><nyugtaszam>NYGTA-2026-1</nyugtaszam><tipus>NY</tipus>'
        .'<stornozott>false</stornozott><kelt>2026-02-09</kelt>'
        .'<fizmod>készpénz</fizmod><penznem>HUF</penznem><teszt>true</teszt></alap>'
        .'<tetelek><tetel><megnevezes>Eladó tétel</megnevezes><mennyiseg>1</mennyiseg>'
        .'<mennyisegiEgyseg>db</mennyisegiEgyseg><nettoEgysegar>10000</nettoEgysegar>'
        .'<afakulcs>27</afakulcs><netto>10000</netto><afa>2700</afa><brutto>12700</brutto>'
        .'</tetel></tetelek>'
        .'<osszegek><totalossz><netto>10000</netto><afa>2700</afa><brutto>12700</brutto></totalossz></osszegek>'
        .'</nyugta>'
        .'</xmlnyugtavalasz>';

    Http::fake([
        'www.szamlazz.hu/*' => Http::response($receiptXml, 200, [
            'Content-Type' => 'application/octet-stream',
        ]),
    ]);

    $client = app(Client::class);
    $result = $client->getReceiptData('NYGTA-2026-1');

    expect($result->isSuccess())->toBeTrue()
        ->and($result->getDocumentNumber())->toBe('NYGTA-2026-1');
});

it('downloads receipt pdf', function () {
    $receiptXml = '<?xml version="1.0" encoding="UTF-8"?>'
        .'<xmlnyugtavalasz>'
        .'<sikeres>true</sikeres>'
        .'<hibakod></hibakod>'
        .'<hibauzenet></hibauzenet>'
        .'<nyugta>'
        .'<alap><id>100</id><nyugtaszam>NYGTA-2026-1</nyugtaszam><tipus>NY</tipus>'
        .'<stornozott>false</stornozott><kelt>2026-02-09</kelt>'
        .'<fizmod>készpénz</fizmod><penznem>HUF</penznem><teszt>true</teszt></alap>'
        .'</nyugta>'
        .'<nyugtaPdf>'.base64_encode('fake-receipt-pdf').'</nyugtaPdf>'
        .'</xmlnyugtavalasz>';

    Http::fake([
        'www.szamlazz.hu/*' => Http::response($receiptXml, 200, [
            'Content-Type' => 'application/octet-stream',
        ]),
    ]);

    $client = app(Client::class);
    $result = $client->getReceiptPdf('NYGTA-2026-1');

    expect($result->isSuccess())->toBeTrue()
        ->and($result->getDocumentNumber())->toBe('NYGTA-2026-1');
});
