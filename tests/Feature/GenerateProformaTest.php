<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Document\Proforma;
use NETipar\Szamlazzhu\Entity\Buyer;
use NETipar\Szamlazzhu\Entity\Seller;
use NETipar\Szamlazzhu\Enums\Currency;
use NETipar\Szamlazzhu\Enums\Language;
use NETipar\Szamlazzhu\Enums\PaymentMethod;
use NETipar\Szamlazzhu\Item\InvoiceItem;

it('generates a proforma successfully', function () {
    Http::fake([
        'www.szamlazz.hu/*' => Http::response(
            base64_decode('JVBERi0xLjQKMSAwIG9iago8PAovVGl0bGUgKP7/AFQAZQBzAHoAdCkKPj4KZW5kb2JqCg=='),
            200,
            [
                'szlahu_szamlaszam' => 'D-TESZT-1',
                'szlahu_id' => '100001',
                'szlahu_nettovegosszeg' => '50000',
                'szlahu_bruttovegosszeg' => '63500',
                'szlahu_kintlevoseg' => '63500',
                'Content-Type' => 'application/pdf',
            ]
        ),
    ]);

    $client = app(Client::class);

    $proforma = new Proforma;
    $header = $proforma->getHeader();
    $header->setIssueDate('2026-02-09');
    $header->setFulfillment('2026-02-09');
    $header->setPaymentDue('2026-02-17');
    $header->setPaymentMethod(PaymentMethod::BankTransfer);
    $header->setCurrency(Currency::HUF);
    $header->setLanguage(Language::Hungarian);

    $seller = new Seller(bankName: 'OTP Bank', bankAccountNumber: '11111111-22222222-33333333');
    $proforma->setSeller($seller);

    $buyer = new Buyer(name: 'Teszt Kft.', zipCode: '1234', city: 'Budapest', address: 'Fő utca 1.');
    $buyer->setTaxNumber('12345678-1-42');
    $proforma->setBuyer($buyer);

    $item = new InvoiceItem;
    $item->setName('Díjbekérő tétel');
    $item->setNetUnitPrice(50000.0);
    $item->setQuantity(1.0);
    $item->setQuantityUnit('db');
    $item->setVat('27');
    $item->setNetPrice(50000.0);
    $item->setVatAmount(13500.0);
    $item->setGrossAmount(63500.0);
    $proforma->addItem($item);

    $result = $client->generateProforma($proforma);

    expect($result->isSuccess())->toBeTrue()
        ->and($result->getDocumentNumber())->toBe('D-TESZT-1')
        ->and($result->getPdfFile())->not->toBeNull();
});

it('deletes a proforma successfully', function () {
    $responseXml = '<?xml version="1.0" encoding="UTF-8"?>'
        .'<xmlszamladbkdelvalasz>'
        .'<sikeres>true</sikeres>'
        .'<hibakod></hibakod>'
        .'<hibauzenet></hibauzenet>'
        .'</xmlszamladbkdelvalasz>';

    Http::fake([
        'www.szamlazz.hu/*' => Http::response($responseXml, 200, [
            'Content-Type' => 'application/octet-stream',
        ]),
    ]);

    $client = app(Client::class);
    $result = $client->deleteProforma('D-TESZT-1');

    expect($result->isSuccess())->toBeTrue();
});
