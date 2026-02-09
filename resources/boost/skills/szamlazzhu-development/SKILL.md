---
name: szamlazzhu-development
description: Build and integrate szamlazz.hu invoicing features using the netipar/szamlazzhu Laravel package, including invoice/receipt generation, PDF downloads, payment recording, and taxpayer queries.
---

# Szamlazz.hu Integration Development

## When to use this skill

Use this skill when:
- Generating invoices, receipts, proforma invoices, or delivery notes via the szamlazz.hu API
- Downloading PDF documents from szamlazz.hu
- Querying invoice/receipt data or taxpayer information
- Recording payments on existing invoices
- Reversing (storno) invoices or receipts
- Configuring the szamlazzhu package

## Package overview

The `netipar/szamlazzhu` package wraps the szamlazz.hu Hungarian invoicing API for Laravel. It uses Laravel HTTP Client, Storage, and Cache facades internally.

- Namespace: `NETipar\Szamlazzhu\`
- Entry point: `NETipar\Szamlazzhu\Client` (registered as singleton)
- Config: `config/szamlazzhu.php`

## Client access

Always obtain the Client via dependency injection or the service container:

```php
use NETipar\Szamlazzhu\Client;

// Dependency injection (preferred)
public function handle(Client $client): void
{
    $result = $client->generateInvoice($invoice);
}

// Or via service container
$client = app(Client::class);
```

## Generate an invoice

```php
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Document\Invoice;
use NETipar\Szamlazzhu\Entity\Buyer;
use NETipar\Szamlazzhu\Entity\Seller;
use NETipar\Szamlazzhu\Enums\Currency;
use NETipar\Szamlazzhu\Enums\Language;
use NETipar\Szamlazzhu\Enums\PaymentMethod;
use NETipar\Szamlazzhu\Item\InvoiceItem;

$invoice = new Invoice();
$header = $invoice->getHeader();
$header->setIssueDate(now()->format('Y-m-d'));
$header->setFulfillment(now()->format('Y-m-d'));
$header->setPaymentDue(now()->addDays(8)->format('Y-m-d'));
$header->setPaymentMethod(PaymentMethod::BankTransfer);
$header->setCurrency(Currency::HUF);
$header->setLanguage(Language::Hungarian);
$header->setComment('Invoice comment');

$seller = new Seller(
    bankName: 'OTP Bank',
    bankAccountNumber: '11111111-22222222-33333333',
);
$invoice->setSeller($seller);

$buyer = new Buyer(
    name: 'Customer Kft.',
    zipCode: '1234',
    city: 'Budapest',
    address: 'Main Street 1.',
);
$buyer->setTaxNumber('12345678-1-42');
$buyer->setEmail('customer@example.com');
$invoice->setBuyer($buyer);

$item = new InvoiceItem();
$item->setName('Product name');
$item->setNetUnitPrice(10000.0);
$item->setQuantity(2.0);
$item->setQuantityUnit('db');
$item->setVat('27');
$item->setNetPrice(20000.0);
$item->setVatAmount(5400.0);
$item->setGrossAmount(25400.0);
$invoice->addItem($item);

$result = $client->generateInvoice($invoice);

$result->isSuccess();          // bool
$result->getDocumentNumber();  // e.g. 'E-PREFIX-2026-001'
$result->getPdfFile();         // PDF binary string or null
```

## Generate a receipt

```php
use NETipar\Szamlazzhu\Document\Receipt;
use NETipar\Szamlazzhu\Entity\Buyer;
use NETipar\Szamlazzhu\Enums\Currency;
use NETipar\Szamlazzhu\Enums\PaymentMethod;
use NETipar\Szamlazzhu\Item\ReceiptItem;

$receipt = new Receipt();
$receipt->getHeader()->setPrefix('NYGTA');
$receipt->getHeader()->setPaymentMethod(PaymentMethod::Cash);
$receipt->getHeader()->setCurrency(Currency::HUF);

$receipt->setBuyer(new Buyer(name: 'Customer Name'));

$item = new ReceiptItem();
$item->setName('Item name');
$item->setNetUnitPrice(10000.0);
$item->setQuantity(1.0);
$item->setQuantityUnit('db');
$item->setVat('27');
$item->setNetPrice(10000.0);
$item->setVatAmount(2700.0);
$item->setGrossAmount(12700.0);
$receipt->addItem($item);

$result = $client->generateReceipt($receipt);
```

## Generate a proforma invoice

```php
use NETipar\Szamlazzhu\Document\Proforma;

$proforma = new Proforma();
// Same structure as Invoice: setHeader, setSeller, setBuyer, addItem
$result = $client->generateProforma($proforma);
```

## Generate a delivery note

```php
use NETipar\Szamlazzhu\Document\DeliveryNote;

$note = new DeliveryNote();
// Same structure as Invoice: setHeader, setSeller, setBuyer, addItem
$result = $client->generateDeliveryNote($note);
```

## Reverse (storno) an invoice

```php
use NETipar\Szamlazzhu\Document\ReverseInvoice;

$invoice = new ReverseInvoice();
$invoice->getHeader()->setInvoiceNumber('E-PREFIX-2026-001');

$result = $client->generateReverseInvoice($invoice);
```

## Reverse (storno) a receipt

```php
use NETipar\Szamlazzhu\Document\ReverseReceipt;

$receipt = new ReverseReceipt();
$receipt->getHeader()->setReceiptNumber('NYGTA-2026-1');

$result = $client->generateReverseReceipt($receipt);
```

## Record a payment on an invoice

```php
use NETipar\Szamlazzhu\CreditNote\InvoiceCreditNote;
use NETipar\Szamlazzhu\Document\Invoice;

$invoice = new Invoice();
$invoice->getHeader()->setInvoiceNumber('E-PREFIX-2026-001');

$creditNote = new InvoiceCreditNote(
    date: now()->format('Y-m-d'),
    amount: 25400.0,
);
$invoice->addCreditNote($creditNote);

$result = $client->payInvoice($invoice);
```

## Query invoice data

```php
use NETipar\Szamlazzhu\Enums\LookupType;

// By invoice number (default)
$result = $client->getInvoiceData('E-PREFIX-2026-001');

// By order number
$result = $client->getInvoiceData('ORD-123', LookupType::OrderNumber);

if ($result->isSuccess()) {
    $data = $result->toArray();
    // $data['result']['alap']['szamlaszam']
    // $data['result']['vevo']['nev']
}
```

## Download invoice PDF

```php
use Illuminate\Support\Facades\Storage;

$result = $client->getInvoicePdf('E-PREFIX-2026-001');
$pdf = $result->toPdf();

if ($pdf) {
    Storage::put('invoices/E-PREFIX-2026-001.pdf', $pdf);
}
```

## Query receipt data

```php
$result = $client->getReceiptData('NYGTA-2026-1');

if ($result->isSuccess()) {
    $data = $result->toArray();
}
```

## Download receipt PDF

```php
$result = $client->getReceiptPdf('NYGTA-2026-1');
$pdf = $result->toPdf();

if ($pdf) {
    Storage::put('receipts/NYGTA-2026-1.pdf', $pdf);
}
```

## Query taxpayer data

```php
// Pass the first 8 digits of the tax number
$result = $client->getTaxPayer('12345678');

if ($result->isSuccess()) {
    $data = $result->toArray();
}
```

## Delete a proforma invoice

```php
use NETipar\Szamlazzhu\Enums\LookupType;

// By proforma number (default)
$result = $client->deleteProforma('D-PREFIX-2026-001');

// By order number
$result = $client->deleteProforma('REND-2026-001', LookupType::OrderNumber);
```

## Available enums

Always use enum cases instead of raw strings:

- `PaymentMethod`: `BankTransfer`, `Cash`, `CreditCard`, `Check`, `CashOnDelivery`, `PayPal`, `Szep`, `Otp`, `Compensation`, `Voucher`, `Barion`, `Other`
- `Currency`: `HUF`, `EUR`, `USD`, `GBP`, `CHF`, and 30+ more ISO currencies
- `Language`: `Hungarian`, `English`, `German`, `Italian`, `Romanian`, `Slovak`, `Croatian`, `French`, `Spanish`, `Czech`, `Polish`
- `VatRate`: `Percent27`, `Percent25`, `Percent20`, `Percent19`, `Percent18`, `Percent7`, `Percent5`, `Percent0`, `TAM`, `AAM`, `EU`, `EUK`, `MAA`, and more
- `LookupType`: `InvoiceNumber`, `OrderNumber`, `ExternalId` - for querying invoices/PDFs by different identifiers
- `DocumentType`: `Invoice`, `ReverseInvoice`, `CorrectiveInvoice`, `PrepaymentInvoice`, `FinalInvoice`, `Proforma`, `DeliveryNote`, `Receipt`, `ReverseReceipt`
- `WaybillType`: `Transoflex`, `Sprinter`, `Ppp`, `Mpl` - for waybill/delivery service types
- `SchemaType`: `Invoice`, `Receipt`, `Proforma`, `TaxPayer` - API response schema types

## Document types

| Class | Purpose |
|---|---|
| `Invoice` | Standard invoice |
| `PrePaymentInvoice` | Pre-payment (advance) invoice |
| `FinalInvoice` | Final invoice |
| `CorrectiveInvoice` | Corrective invoice |
| `ReverseInvoice` | Reverse (storno) invoice |
| `Receipt` | Receipt |
| `ReverseReceipt` | Reverse (storno) receipt |
| `Proforma` | Proforma invoice |
| `DeliveryNote` | Delivery note |

All document classes live in `NETipar\Szamlazzhu\Document\`.

## Response handling

Every Client method returns `NETipar\Szamlazzhu\Http\ApiResponse`:

```php
$result->isSuccess();          // bool
$result->isFailed();           // bool
$result->getDocumentNumber();  // string|null
$result->getPdfFile();         // string|null (binary PDF)
$result->toPdf();              // string|null (alias)
$result->toArray();            // array|null
$result->toJson();             // string|null
$result->toXml();              // string|null
$result->getErrorMsg();        // string|null
$result->getErrorCode();       // int|null
```

## Error handling

```php
use NETipar\Szamlazzhu\Exceptions\SzamlazzhuException;
use NETipar\Szamlazzhu\Exceptions\ConnectionException;
use NETipar\Szamlazzhu\Exceptions\ResponseException;
use NETipar\Szamlazzhu\Exceptions\ValidationException;
use NETipar\Szamlazzhu\Exceptions\XmlBuildException;

try {
    $result = $client->generateInvoice($invoice);
} catch (ConnectionException $e) {
    // Network / connection error
} catch (ResponseException $e) {
    // API response error
} catch (ValidationException $e) {
    // Input validation error
} catch (XmlBuildException $e) {
    // XML generation error
} catch (SzamlazzhuException $e) {
    // Base exception (catches all above)
}
```

## Configuration

Key `.env` variables:

```
SZAMLAZZHU_API_KEY=your-agent-key
SZAMLAZZHU_DOWNLOAD_PDF=true
SZAMLAZZHU_SAVE_PDF=true
SZAMLAZZHU_TIMEOUT=30
SZAMLAZZHU_SESSION_DRIVER=cache
```

Publish config with: `php artisan vendor:publish --tag=szamlazzhu-config`

## Entity constructors

All entity, header, item, and document properties are **public** — they can be read and written directly. Fluent setters are also available for chaining.

Buyer supports constructor arguments, direct property access, and fluent setters:

```php
$buyer = new Buyer(
    name: 'Company Name',
    zipCode: '1234',
    city: 'Budapest',
    address: 'Street 1.',
);

// Direct property access
$buyer->taxNumber = '12345678-1-42';
$buyer->email = 'email@example.com';

// Or fluent setters (chainable)
$buyer->setPhoneNumber('+36201234567')->setComment('Note');
```

Seller uses constructor promotion:

```php
$seller = new Seller(
    bankName: 'Bank Name',
    bankAccountNumber: '11111111-22222222-33333333',
    emailReplyTo: 'reply@example.com',
    emailSubject: 'Invoice',
    emailContent: 'Your invoice is attached.',
    signatoryName: 'John Doe',
);
```
