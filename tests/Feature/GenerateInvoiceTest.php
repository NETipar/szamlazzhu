<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Document\Invoice;
use NETipar\Szamlazzhu\Entity\Buyer;
use NETipar\Szamlazzhu\Entity\Seller;
use NETipar\Szamlazzhu\Enums\Currency;
use NETipar\Szamlazzhu\Enums\Language;
use NETipar\Szamlazzhu\Enums\PaymentMethod;
use NETipar\Szamlazzhu\Item\InvoiceItem;

it('generates an invoice successfully', function () {
    Http::fake([
        'www.szamlazz.hu/*' => Http::response(
            base64_decode('JVBERi0xLjQKMSAwIG9iago8PAovVGl0bGUgKP7/AFQAZQBzAHoAdCkKPj4KZW5kb2JqCg=='),
            200,
            [
                'szlahu_szamlaszam' => 'E-TESZT-2026-001',
                'szlahu_id' => '123456789',
                'szlahu_nettovegosszeg' => '20000',
                'szlahu_bruttovegosszeg' => '25400',
                'szlahu_kintlevoseg' => '25400',
                'szlahu_vevoifiokurl' => 'https%3A%2F%2Fwww.szamlazz.hu%2Fszamla%2F',
                'Content-Type' => 'application/pdf',
            ]
        ),
    ]);

    $client = app(Client::class);

    $invoice = new Invoice;
    $header = $invoice->getHeader();
    $header->setIssueDate('2026-02-09');
    $header->setFulfillment('2026-02-09');
    $header->setPaymentDue('2026-02-17');
    $header->setPaymentMethod(PaymentMethod::BankTransfer);
    $header->setCurrency(Currency::HUF);
    $header->setLanguage(Language::Hungarian);
    $header->setComment('Teszt számla');

    $seller = new Seller(bankName: 'OTP Bank', bankAccountNumber: '11111111-22222222-33333333');
    $invoice->setSeller($seller);

    $buyer = new Buyer(name: 'Teszt Kft.', zipCode: '1234', city: 'Budapest', address: 'Fő utca 1.');
    $buyer->setTaxNumber('12345678-1-42');
    $buyer->setEmail('test@example.com');
    $invoice->setBuyer($buyer);

    $item = new InvoiceItem;
    $item->setName('Teszt termék');
    $item->setNetUnitPrice(10000.0);
    $item->setQuantity(2.0);
    $item->setQuantityUnit('db');
    $item->setVat('27');
    $item->setNetPrice(20000.0);
    $item->setVatAmount(5400.0);
    $item->setGrossAmount(25400.0);
    $invoice->addItem($item);

    $result = $client->generateInvoice($invoice);

    expect($result->isSuccess())->toBeTrue()
        ->and($result->getDocumentNumber())->toBe('E-TESZT-2026-001')
        ->and($result->getPdfFile())->not->toBeNull();

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'szamlazz.hu');
    });
});

it('builds correct invoice xml with all fields', function () {
    $invoice = new Invoice;
    $header = $invoice->getHeader();
    $header->setIssueDate('2026-02-09');
    $header->setFulfillment('2026-02-09');
    $header->setPaymentDue('2026-02-17');
    $header->setPaymentMethod(PaymentMethod::BankTransfer);
    $header->setCurrency(Currency::HUF);
    $header->setLanguage(Language::Hungarian);

    $seller = new Seller(bankName: 'OTP Bank', bankAccountNumber: '11111111-22222222-33333333');
    $invoice->setSeller($seller);

    $buyer = new Buyer(name: 'Teszt Kft.', zipCode: '1234', city: 'Budapest', address: 'Fő utca 1.');
    $buyer->setTaxNumber('12345678-1-42');
    $invoice->setBuyer($buyer);

    $item = new InvoiceItem;
    $item->setName('Teszt termék');
    $item->setNetUnitPrice(10000.0);
    $item->setQuantity(2.0);
    $item->setQuantityUnit('db');
    $item->setVat('27');
    $item->setNetPrice(20000.0);
    $item->setVatAmount(5400.0);
    $item->setGrossAmount(25400.0);
    $invoice->addItem($item);

    $settings = ['szamlaagentkulcs' => 'test-key'];
    $xmlArray = $invoice->toXmlArray($settings);

    expect($xmlArray)->toHaveKey('beallitasok')
        ->and($xmlArray)->toHaveKey('fejlec')
        ->and($xmlArray)->toHaveKey('elado')
        ->and($xmlArray)->toHaveKey('vevo')
        ->and($xmlArray)->toHaveKey('tetelek')
        ->and($xmlArray['fejlec']['fizmod'])->toBe('átutalás')
        ->and($xmlArray['fejlec']['penznem'])->toBe('HUF')
        ->and($xmlArray['fejlec']['szamlaNyelve'])->toBe('hu')
        ->and($xmlArray['elado']['bank'])->toBe('OTP Bank')
        ->and($xmlArray['vevo']['nev'])->toBe('Teszt Kft.')
        ->and($xmlArray['vevo']['adoszam'])->toBe('12345678-1-42')
        ->and($xmlArray['tetelek'])->toHaveCount(1)
        ->and($xmlArray['tetelek']['item0']['megnevezes'])->toBe('Teszt termék');
});
