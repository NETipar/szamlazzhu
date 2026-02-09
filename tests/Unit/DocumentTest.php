<?php

use NETipar\Szamlazzhu\Document\Invoice;
use NETipar\Szamlazzhu\Entity\Buyer;
use NETipar\Szamlazzhu\Entity\Seller;
use NETipar\Szamlazzhu\Enums\Currency;
use NETipar\Szamlazzhu\Enums\Language;
use NETipar\Szamlazzhu\Enums\PaymentMethod;
use NETipar\Szamlazzhu\Header\InvoiceHeader;
use NETipar\Szamlazzhu\Item\InvoiceItem;

it('creates an invoice with header', function () {
    $invoice = new Invoice;

    expect($invoice->getHeader())->toBeInstanceOf(InvoiceHeader::class)
        ->and($invoice->getHeader()->isInvoice())->toBeTrue();
});

it('builds invoice xml array with settings, header, buyer, seller', function () {
    $invoice = new Invoice;

    $invoice->getHeader()
        ->setIssueDate('2025-01-15')
        ->setFulfillment('2025-01-15')
        ->setPaymentDue('2025-01-30')
        ->setPaymentMethod(PaymentMethod::BankTransfer)
        ->setCurrency(Currency::HUF)
        ->setLanguage(Language::Hungarian)
        ->setComment('Test invoice');

    $buyer = new Buyer;
    $buyer->setName('Test Buyer')
        ->setZipCode('1000')
        ->setCity('Budapest')
        ->setAddress('Test utca 1.');
    $invoice->setBuyer($buyer);

    $seller = new Seller;
    $seller->setBankName('Test Bank');
    $invoice->setSeller($seller);

    $settings = ['szamlaagentkulcs' => 'test-key'];
    $xml = $invoice->toXmlArray($settings);

    expect($xml)->toHaveKeys(['beallitasok', 'fejlec', 'elado', 'vevo'])
        ->and($xml['beallitasok']['szamlaagentkulcs'])->toBe('test-key')
        ->and($xml['fejlec']['keltDatum'])->toBe('2025-01-15')
        ->and($xml['fejlec']['fizmod'])->toBe('átutalás')
        ->and($xml['fejlec']['penznem'])->toBe('HUF')
        ->and($xml['vevo']['nev'])->toBe('Test Buyer')
        ->and($xml['elado']['bank'])->toBe('Test Bank');
});

it('adds items to invoice xml array', function () {
    $invoice = new Invoice;

    $item = new InvoiceItem;
    $item->setName('Test Item')
        ->setQuantity(2.0)
        ->setQuantityUnit('db')
        ->setNetUnitPrice(1000.0)
        ->setVat('27')
        ->setNetPrice(2000.0)
        ->setVatAmount(540.0)
        ->setGrossAmount(2540.0);

    $invoice->addItem($item);

    $xml = $invoice->toXmlArray(['szamlaagentkulcs' => 'key']);

    expect($xml)->toHaveKey('tetelek')
        ->and($xml['tetelek'])->toHaveKey('item0')
        ->and($xml['tetelek']['item0']['megnevezes'])->toBe('Test Item')
        ->and($xml['tetelek']['item0']['mennyiseg'])->toBe(2.0)
        ->and($xml['tetelek']['item0']['nettoEgysegar'])->toBe(1000.0)
        ->and($xml['tetelek']['item0']['afakulcs'])->toBe('27');
});

it('supports invoice constants', function () {
    expect(Invoice::FROM_INVOICE_NUMBER)->toBe(1)
        ->and(Invoice::FROM_ORDER_NUMBER)->toBe(2)
        ->and(Invoice::FROM_INVOICE_EXTERNAL_ID)->toBe(3)
        ->and(Invoice::CREDIT_NOTES_LIMIT)->toBe(5)
        ->and(Invoice::ATTACHMENTS_LIMIT)->toBe(5);
});

it('builds reverse invoice xml array', function () {
    $invoice = new Invoice;
    $invoice->getHeader()->setInvoiceNumber('TST-2025-001');

    $buyer = new Buyer;
    $buyer->setName('Buyer');
    $invoice->setBuyer($buyer);

    $settings = ['szamlaagentkulcs' => 'key'];
    $xml = $invoice->toReverseXmlArray($settings);

    expect($xml)->toHaveKeys(['beallitasok', 'fejlec', 'vevo'])
        ->and($xml)->not->toHaveKey('tetelek');
});

it('builds credits xml array', function () {
    $invoice = new Invoice;

    $settings = ['szamlaagentkulcs' => 'key', 'szamlaszam' => 'TST-001'];
    $xml = $invoice->toCreditsXmlArray($settings);

    expect($xml)->toHaveKey('beallitasok')
        ->and($xml['beallitasok']['szamlaszam'])->toBe('TST-001');
});

it('invoice header supports Carbon and string dates', function () {
    $header = new InvoiceHeader;
    $header->setIssueDate('2025-03-15');

    $xml = $header->toXmlArray();

    expect($xml['keltDatum'])->toBe('2025-03-15');
});

it('invoice header supports enum and string payment method', function () {
    $header = new InvoiceHeader;

    $header->setPaymentMethod(PaymentMethod::Cash);
    $xml = $header->toXmlArray();
    expect($xml['fizmod'])->toBe('készpénz');

    $header->setPaymentMethod('egyedi fizetési mód');
    $xml = $header->toXmlArray();
    expect($xml['fizmod'])->toBe('egyedi fizetési mód');
});

it('invoice header sets document type flags', function () {
    $header = new InvoiceHeader;

    expect($header->isInvoice())->toBeTrue()
        ->and($header->isProforma())->toBeFalse()
        ->and($header->isDeliveryNote())->toBeFalse()
        ->and($header->isPrePayment())->toBeFalse()
        ->and($header->isFinal())->toBeFalse()
        ->and($header->isCorrective())->toBeFalse();
});
