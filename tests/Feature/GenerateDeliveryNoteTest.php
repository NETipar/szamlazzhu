<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Document\DeliveryNote;
use NETipar\Szamlazzhu\Entity\Buyer;
use NETipar\Szamlazzhu\Entity\Seller;
use NETipar\Szamlazzhu\Enums\Currency;
use NETipar\Szamlazzhu\Enums\Language;
use NETipar\Szamlazzhu\Enums\PaymentMethod;
use NETipar\Szamlazzhu\Item\InvoiceItem;

it('generates a delivery note successfully', function () {
    Http::fake([
        'www.szamlazz.hu/*' => Http::response(
            base64_decode('JVBERi0xLjQKMSAwIG9iago8PAovVGl0bGUgKP7/AFQAZQBzAHoAdCkKPj4KZW5kb2JqCg=='),
            200,
            [
                'szlahu_szamlaszam' => 'S-TESZT-2026-1',
                'szlahu_id' => '200001',
                'szlahu_nettovegosszeg' => '15000',
                'szlahu_bruttovegosszeg' => '19050',
                'szlahu_kintlevoseg' => '19050',
                'Content-Type' => 'application/pdf',
            ]
        ),
    ]);

    $client = app(Client::class);

    $note = new DeliveryNote;
    $header = $note->getHeader();
    $header->setIssueDate('2026-02-09');
    $header->setFulfillment('2026-02-09');
    $header->setPaymentDue('2026-02-17');
    $header->setPaymentMethod(PaymentMethod::BankTransfer);
    $header->setCurrency(Currency::HUF);
    $header->setLanguage(Language::Hungarian);

    $seller = new Seller(bankName: 'OTP Bank', bankAccountNumber: '11111111-22222222-33333333');
    $note->setSeller($seller);

    $buyer = new Buyer(name: 'Teszt Kft.', zipCode: '1234', city: 'Budapest', address: 'Fő utca 1.');
    $note->setBuyer($buyer);

    $item = new InvoiceItem;
    $item->setName('Szállított termék');
    $item->setNetUnitPrice(5000.0);
    $item->setQuantity(3.0);
    $item->setQuantityUnit('db');
    $item->setVat('27');
    $item->setNetPrice(15000.0);
    $item->setVatAmount(4050.0);
    $item->setGrossAmount(19050.0);
    $note->addItem($item);

    $result = $client->generateDeliveryNote($note);

    expect($result->isSuccess())->toBeTrue()
        ->and($result->getDocumentNumber())->toBe('S-TESZT-2026-1')
        ->and($result->getPdfFile())->not->toBeNull();
});
