# Proforma Invoice and Delivery Note

The `netipar/szamlazzhu` package supports generating proforma invoices and delivery notes, as well as deleting proforma invoices.

## Generate Proforma Invoice

Creating and sending a proforma invoice through the Szamlazz.hu API.

```php
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Document\Proforma;
use NETipar\Szamlazzhu\Entity\Buyer;
use NETipar\Szamlazzhu\Entity\Seller;
use NETipar\Szamlazzhu\Enums\Currency;
use NETipar\Szamlazzhu\Enums\Language;
use NETipar\Szamlazzhu\Enums\PaymentMethod;
use NETipar\Szamlazzhu\Item\InvoiceItem;

$client = app(Client::class);

$proforma = new Proforma();

$header = $proforma->getHeader();
$header->setIssueDate(now()->format('Y-m-d'));
$header->setFulfillment(now()->format('Y-m-d'));
$header->setPaymentDue(now()->addDays(8)->format('Y-m-d'));
$header->setPaymentMethod(PaymentMethod::BankTransfer);
$header->setCurrency(Currency::HUF);
$header->setLanguage(Language::Hungarian);
$header->setComment('Test proforma invoice');

$seller = new Seller(
    bankName: 'OTP Bank',
    bankAccountNumber: '11111111-22222222-33333333',
);
$proforma->setSeller($seller);

$buyer = new Buyer(
    name: 'Teszt Kft.',
    zipCode: '1234',
    city: 'Budapest',
    address: 'Fo utca 1.',
);
$buyer->setTaxNumber('12345678-1-42');
$proforma->setBuyer($buyer);

$item = new InvoiceItem();
$item->setName('Proforma invoice item');
$item->setNetUnitPrice(50000.0);
$item->setQuantity(1.0);
$item->setQuantityUnit('db');
$item->setVat('27');
$item->setNetPrice(50000.0);
$item->setVatAmount(13500.0);
$item->setGrossAmount(63500.0);
$proforma->addItem($item);

$result = $client->generateProforma($proforma);

$result->isSuccess();
$result->getDocumentNumber();
```

## Delete Proforma Invoice

Deleting an already created proforma invoice by its document number.

```php
use NETipar\Szamlazzhu\Client;

$client = app(Client::class);
$result = $client->deleteProforma('D-TESZT-2026-001');

$result->isSuccess();
$result->getDocumentNumber();
```

Deletion by order number is also possible using the `LookupType::OrderNumber` enum:

```php
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Enums\LookupType;

$client = app(Client::class);
$result = $client->deleteProforma('REND-2026-001', LookupType::OrderNumber);
```

## Generate Delivery Note

Creating and sending a delivery note through the API. The structure of a delivery note is the same as an invoice.

```php
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Document\DeliveryNote;
use NETipar\Szamlazzhu\Entity\Buyer;
use NETipar\Szamlazzhu\Entity\Seller;
use NETipar\Szamlazzhu\Enums\Currency;
use NETipar\Szamlazzhu\Enums\Language;
use NETipar\Szamlazzhu\Enums\PaymentMethod;
use NETipar\Szamlazzhu\Item\InvoiceItem;

$client = app(Client::class);

$note = new DeliveryNote();

$header = $note->getHeader();
$header->setIssueDate(now()->format('Y-m-d'));
$header->setFulfillment(now()->format('Y-m-d'));
$header->setPaymentDue(now()->addDays(8)->format('Y-m-d'));
$header->setPaymentMethod(PaymentMethod::BankTransfer);
$header->setCurrency(Currency::HUF);
$header->setLanguage(Language::Hungarian);
$header->setComment('Test delivery note');

$seller = new Seller(
    bankName: 'OTP Bank',
    bankAccountNumber: '11111111-22222222-33333333',
);
$note->setSeller($seller);

$buyer = new Buyer(
    name: 'Teszt Kft.',
    zipCode: '1234',
    city: 'Budapest',
    address: 'Fo utca 1.',
);
$note->setBuyer($buyer);

$item = new InvoiceItem();
$item->setName('Delivered product');
$item->setNetUnitPrice(5000.0);
$item->setQuantity(3.0);
$item->setQuantityUnit('db');
$item->setVat('27');
$item->setNetPrice(15000.0);
$item->setVatAmount(4050.0);
$item->setGrossAmount(19050.0);
$note->addItem($item);

$result = $client->generateDeliveryNote($note);

$result->isSuccess();
$result->getDocumentNumber();
$result->getPdfFile();
```
