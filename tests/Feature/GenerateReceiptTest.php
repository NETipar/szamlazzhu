<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Document\Receipt;
use NETipar\Szamlazzhu\Entity\Buyer;
use NETipar\Szamlazzhu\Enums\Currency;
use NETipar\Szamlazzhu\Enums\PaymentMethod;
use NETipar\Szamlazzhu\Item\ReceiptItem;

it('generates a receipt successfully', function () {
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
        .'<kifizetesek><kifizetes><fizetoeszkoz>készpénz</fizetoeszkoz><osszeg>12700</osszeg></kifizetes></kifizetesek>'
        .'</nyugta>'
        .'<nyugtaPdf>'.base64_encode('fake-pdf-content').'</nyugtaPdf>'
        .'</xmlnyugtavalasz>';

    Http::fake([
        'www.szamlazz.hu/*' => Http::response($receiptXml, 200, [
            'Content-Type' => 'application/octet-stream',
        ]),
    ]);

    $client = app(Client::class);

    $receipt = new Receipt;
    $header = $receipt->getHeader();
    $header->setPrefix('NYGTA');
    $header->setPaymentMethod(PaymentMethod::Cash);
    $header->setCurrency(Currency::HUF);
    $header->setComment('Teszt nyugta');

    $buyer = new Buyer(name: 'Teszt Vásárló');
    $receipt->setBuyer($buyer);

    $item = new ReceiptItem;
    $item->setName('Eladó tétel');
    $item->setNetUnitPrice(10000.0);
    $item->setQuantity(1.0);
    $item->setQuantityUnit('db');
    $item->setVat('27');
    $item->setNetPrice(10000.0);
    $item->setVatAmount(2700.0);
    $item->setGrossAmount(12700.0);
    $receipt->addItem($item);

    $result = $client->generateReceipt($receipt);

    expect($result->isSuccess())->toBeTrue()
        ->and($result->getDocumentNumber())->toBe('NYGTA-2026-1');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'szamlazz.hu');
    });
});

it('builds correct receipt xml with all fields', function () {
    $receipt = new Receipt;
    $header = $receipt->getHeader();
    $header->setPrefix('NYGTA');
    $header->setPaymentMethod(PaymentMethod::Cash);
    $header->setCurrency(Currency::HUF);
    $header->setComment('Teszt nyugta');

    $buyer = new Buyer(name: 'Teszt Vásárló');
    $receipt->setBuyer($buyer);

    $item = new ReceiptItem;
    $item->setName('Eladó tétel');
    $item->setNetUnitPrice(10000.0);
    $item->setQuantity(1.0);
    $item->setQuantityUnit('db');
    $item->setVat('27');
    $item->setNetPrice(10000.0);
    $item->setVatAmount(2700.0);
    $item->setGrossAmount(12700.0);
    $receipt->addItem($item);

    $settings = ['szamlaagentkulcs' => 'test-key'];
    $xmlArray = $receipt->toXmlArray($settings);

    expect($xmlArray)->toHaveKey('beallitasok')
        ->and($xmlArray)->toHaveKey('fejlec')
        ->and($xmlArray)->toHaveKey('tetelek')
        ->and($xmlArray['fejlec']['fizmod'])->toBe('készpénz')
        ->and($xmlArray['fejlec']['penznem'])->toBe('HUF')
        ->and($xmlArray['fejlec']['elotag'])->toBe('NYGTA')
        ->and($xmlArray['tetelek'])->toHaveCount(1);
});
