# Számlák

A `netipar/szamlazzhu` csomag számla-műveleteinek használata.

## Tartalomjegyzék

- [Számla generálás](#számla-generálás)
- [Sztornó számla](#sztornó-számla)
- [Számla adatok lekérdezése](#számla-adatok-lekérdezése)
- [Számla PDF letöltése](#számla-pdf-letöltése)
- [Számla kifizetés rögzítése](#számla-kifizetés-rögzítése)

---

## Számla generálás

Új számla létrehozása és elküldése a szamlazz.hu rendszerébe. A számla tartalmazza az eladón, a vevőn és a tételeken kívül a fizetési és nyelvi beállításokat is.

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
$header->setComment('Teszt számla');

$seller = new Seller(
    bankName: 'OTP Bank',
    bankAccountNumber: '11111111-22222222-33333333',
);
$invoice->setSeller($seller);

$buyer = new Buyer(
    name: 'Teszt Kft.',
    zipCode: '1234',
    city: 'Budapest',
    address: 'Fő utca 1.',
);
$buyer->setTaxNumber('12345678-1-42');
$buyer->setEmail('test@example.com');
$invoice->setBuyer($buyer);

$item = new InvoiceItem();
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

// Eredmény kezelés
$result->isSuccess();          // bool - sikeres volt-e a művelet
$result->getDocumentNumber();  // 'E-HGDS-2026-318' - a generált számla száma
$result->getPdfFile();         // PDF tartalom (binary string) vagy null
```

---

## Sztornó számla

Egy már kiállított számla sztornózása a számla számának megadásával.

```php
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Document\ReverseInvoice;

$client = app(Client::class);

$invoice = new ReverseInvoice();
$invoice->getHeader()->setInvoiceNumber('E-TESZT-2026-001');

$result = $client->generateReverseInvoice($invoice);

// Eredmény kezelés
$result->isSuccess();          // bool
$result->getDocumentNumber();  // A sztornó számla száma
$result->getPdfFile();         // PDF tartalom vagy null
```

---

## Számla adatok lekérdezése

Egy korábban kiállított számla adatainak lekérdezése számlaszám alapján. A válasz XML formátumban tartalmazza a számla összes adatát.

```php
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Enums\LookupType;

$client = app(Client::class);

// Lekérdezés számlaszám alapján (alapértelmezett)
$result = $client->getInvoiceData('E-TESZT-2026-001');

// Lekérdezés rendelésszám alapján
$result = $client->getInvoiceData('ORD-123', LookupType::OrderNumber);

if ($result->isSuccess()) {
    $data = $result->toArray();
    // $data['result']['alap']['szamlaszam']
    // $data['result']['vevo']['nev']
}
```

---

## Számla PDF letöltése

Egy korábban kiállított számla PDF változatának letöltése.

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

## Számla kifizetés rögzítése

Egy már kiállított számla kifizetettnek jelölése jóváírás rögzítésével.

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

// Eredmény kezelés
$result->isSuccess();          // bool
$result->getDocumentNumber();  // A számla száma
```
