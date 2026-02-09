# Invoices

Usage of invoice operations in the `netipar/szamlazzhu` package.

## Table of Contents

- [Generate Invoice](#generate-invoice)
- [Reverse Invoice (Storno)](#reverse-invoice-storno)
- [Query Invoice Data](#query-invoice-data)
- [Download Invoice PDF](#download-invoice-pdf)
- [Record Invoice Payment](#record-invoice-payment)

---

## Generate Invoice

Create a new invoice and submit it to the szamlazz.hu system. The invoice contains the seller, buyer, and line items, as well as payment and language settings.

```php
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Document\Invoice;
use NETipar\Szamlazzhu\Entity\Buyer;
use NETipar\Szamlazzhu\Entity\Seller;
use NETipar\Szamlazzhu\Enums\Currency;
use NETipar\Szamlazzhu\Enums\Language;
use NETipar\Szamlazzhu\Enums\PaymentMethod;
use NETipar\Szamlazzhu\Item\InvoiceItem;

$client = app(Client::class);

$invoice = new Invoice();
$header = $invoice->getHeader();
$header->setIssueDate(now()->format('Y-m-d'));
$header->setFulfillment(now()->format('Y-m-d'));
$header->setPaymentDue(now()->addDays(8)->format('Y-m-d'));
$header->setPaymentMethod(PaymentMethod::BankTransfer);
$header->setCurrency(Currency::HUF);
$header->setLanguage(Language::Hungarian);
$header->setComment('Test invoice');

$seller = new Seller(
    bankName: 'OTP Bank',
    bankAccountNumber: '11111111-22222222-33333333',
);
$invoice->setSeller($seller);

$buyer = new Buyer(
    name: 'Test Ltd.',
    zipCode: '1234',
    city: 'Budapest',
    address: 'Main Street 1.',
);
$buyer->setTaxNumber('12345678-1-42');
$buyer->setEmail('test@example.com');
$invoice->setBuyer($buyer);

$item = new InvoiceItem();
$item->setName('Test product');
$item->setNetUnitPrice(10000.0);
$item->setQuantity(2.0);
$item->setQuantityUnit('pcs');
$item->setVat('27');
$item->setNetPrice(20000.0);
$item->setVatAmount(5400.0);
$item->setGrossAmount(25400.0);
$invoice->addItem($item);

$result = $client->generateInvoice($invoice);

// Result handling
$result->isSuccess();          // bool - whether the operation was successful
$result->getDocumentNumber();  // 'E-HGDS-2026-318' - the generated invoice number
$result->getPdfFile();         // PDF content (binary string) or null
```

---

## Reverse Invoice (Storno)

Reverse (storno) a previously issued invoice by providing the invoice number.

```php
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Document\ReverseInvoice;

$client = app(Client::class);

$invoice = new ReverseInvoice();
$invoice->getHeader()->setInvoiceNumber('E-TESZT-2026-001');

$result = $client->generateReverseInvoice($invoice);

// Result handling
$result->isSuccess();          // bool
$result->getDocumentNumber();  // The storno invoice number
$result->getPdfFile();         // PDF content or null
```

---

## Query Invoice Data

Query the data of a previously issued invoice by invoice number. The response contains all invoice data in XML format.

```php
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Enums\LookupType;

$client = app(Client::class);

// Query by invoice number (default)
$result = $client->getInvoiceData('E-TESZT-2026-001');

// Query by order number
$result = $client->getInvoiceData('ORD-123', LookupType::OrderNumber);

if ($result->isSuccess()) {
    $data = $result->toArray();
    // $data['result']['alap']['szamlaszam']
    // $data['result']['vevo']['nev']
}
```

---

## Download Invoice PDF

Download the PDF version of a previously issued invoice.

```php
use Illuminate\Support\Facades\Storage;
use NETipar\Szamlazzhu\Client;

$client = app(Client::class);
$result = $client->getInvoicePdf('E-TESZT-2026-001');
$pdf = $result->toPdf();

if ($pdf) {
    Storage::put('invoices/E-TESZT-2026-001.pdf', $pdf);
}
```

---

## Record Invoice Payment

Mark a previously issued invoice as paid by recording a credit note.

```php
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\CreditNote\InvoiceCreditNote;
use NETipar\Szamlazzhu\Document\Invoice;

$client = app(Client::class);

$invoice = new Invoice();
$invoice->getHeader()->setInvoiceNumber('E-TESZT-2026-001');

$creditNote = new InvoiceCreditNote(
    date: now()->format('Y-m-d'),
    amount: 25400.0,
);
$invoice->addCreditNote($creditNote);

$result = $client->payInvoice($invoice);

// Result handling
$result->isSuccess();          // bool
$result->getDocumentNumber();  // The invoice number
```
