<?php

use NETipar\Szamlazzhu\Document\Invoice;
use NETipar\Szamlazzhu\Entity\Buyer;
use NETipar\Szamlazzhu\Entity\Seller;
use NETipar\Szamlazzhu\Enums\Currency;
use NETipar\Szamlazzhu\Enums\Language;
use NETipar\Szamlazzhu\Enums\PaymentMethod;
use NETipar\Szamlazzhu\Exceptions\SzamlazzhuException;
use NETipar\Szamlazzhu\Exceptions\ValidationException;
use NETipar\Szamlazzhu\Header\CorrectiveInvoiceHeader;
use NETipar\Szamlazzhu\Header\DeliveryNoteHeader;
use NETipar\Szamlazzhu\Header\FinalInvoiceHeader;
use NETipar\Szamlazzhu\Header\InvoiceHeader;
use NETipar\Szamlazzhu\Header\PrePaymentInvoiceHeader;
use NETipar\Szamlazzhu\Header\ProformaHeader;
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

it('emits simpleItems as the last invoice header element', function () {
    $header = new InvoiceHeader;
    $header->setPreviewPdf(true)->setSimpleItems(true);

    $xml = $header->toXmlArray();

    expect($xml['simpleItems'])->toBeTrue()
        ->and(array_slice(array_keys($xml), -2))->toBe(['elonezetpdf', 'simpleItems']);
});

it('omits simpleItems from the invoice header unless it is enabled', function (?bool $simpleItems) {
    $header = new InvoiceHeader;
    $header->setSimpleItems($simpleItems);

    expect($header->toXmlArray())->not->toHaveKey('simpleItems');
})->with([
    'not set' => [null],
    'disabled' => [false],
]);

it('allows simplified items on the other invoice-family documents', function (InvoiceHeader $header) {
    $header->setSimpleItems(true);

    expect($header->toXmlArray()['simpleItems'])->toBeTrue();
})->with([
    'proforma' => fn () => new ProformaHeader,
    'prepayment invoice' => fn () => new PrePaymentInvoiceHeader,
    'final invoice' => fn () => new FinalInvoiceHeader,
]);

it('rejects simplified items on delivery notes and corrective invoices', function (InvoiceHeader $header) {
    $header->setSimpleItems(true);

    expect(fn () => $header->toXmlArray())
        ->toThrow(ValidationException::class, SzamlazzhuException::SIMPLE_ITEMS_NOT_ALLOWED);
})->with([
    'delivery note' => fn () => new DeliveryNoteHeader,
    'corrective invoice' => fn () => new CorrectiveInvoiceHeader,
]);

it('still emits the deprecated margin scheme VAT fields', function () {
    $header = new InvoiceHeader;
    $header->profitVat = true;

    $item = new InvoiceItem;
    $item->priceGapVatBase = 1000.0;

    expect($header->toXmlArray()['arresAfa'])->toBeTrue()
        ->and($item->toXmlArray()['arresAfaAlap'])->toBe(1000.0);
});

it('reports the margin scheme VAT setters as deprecated', function (string $method) {
    $deprecations = [];

    set_error_handler(function (int $level, string $message) use (&$deprecations): bool {
        $deprecations[] = $message;

        return true;
    }, E_USER_DEPRECATED);

    try {
        match ($method) {
            'setProfitVat' => (new InvoiceHeader)->setProfitVat(true),
            'setPriceGapVatBase' => (new InvoiceItem)->setPriceGapVatBase(1000.0),
        };
    } finally {
        restore_error_handler();
    }

    expect($deprecations)->toHaveCount(1)
        ->and($deprecations[0])->toContain($method, '2027-02-15');
})->with(['setProfitVat', 'setPriceGapVatBase'])
    ->skip(PHP_VERSION_ID < 80400, 'PHP enforces the #[Deprecated] attribute from 8.4 on');
