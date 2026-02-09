# Nyugtak (Receipts)

A `netipar/szamlazzhu` csomag nyugtakezelési műveletei.

---

## Nyugta generálás

Nyugta készítése tételek megadásával és elküldésével a szamlazz.hu API-n keresztül.

```php
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Document\Receipt;
use NETipar\Szamlazzhu\Entity\Buyer;
use NETipar\Szamlazzhu\Enums\Currency;
use NETipar\Szamlazzhu\Enums\PaymentMethod;
use NETipar\Szamlazzhu\Item\ReceiptItem;

$client = app(Client::class);

$receipt = new Receipt();

$header = $receipt->getHeader();
$header->setPrefix('NYGTA');
$header->setPaymentMethod(PaymentMethod::Cash);
$header->setCurrency(Currency::HUF);
$header->setComment('Teszt nyugta');

$buyer = new Buyer(name: 'Teszt Vasarlo');
$receipt->setBuyer($buyer);

$item = new ReceiptItem();
$item->setName('Elado tetel');
$item->setNetUnitPrice(10000.0);
$item->setQuantity(1.0);
$item->setQuantityUnit('db');
$item->setVat('27');
$item->setNetPrice(10000.0);
$item->setVatAmount(2700.0);
$item->setGrossAmount(12700.0);
$receipt->addItem($item);

$result = $client->generateReceipt($receipt);

$result->isSuccess();          // bool
$result->getDocumentNumber();  // 'NYGTA-2026-1'
```

---

## Sztornó nyugta

Egy korábban kiállított nyugta sztornózása a nyugtaszám alapján.

```php
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Document\ReverseReceipt;

$client = app(Client::class);

$receipt = new ReverseReceipt();
$receipt->getHeader()->setReceiptNumber('NYGTA-2026-1');

$result = $client->generateReverseReceipt($receipt);

$result->isSuccess();          // bool
$result->getDocumentNumber();  // A sztornó nyugta száma
```

---

## Nyugta adatok lekérdezése

Egy nyugta adatainak lekérdezése a nyugtaszám alapján JSON/XML formátumban.

```php
use NETipar\Szamlazzhu\Client;

$client = app(Client::class);
$result = $client->getReceiptData('NYGTA-2026-1');

if ($result->isSuccess()) {
    $data = $result->toArray();
    // JSON formátumú nyugta adatok
}
```

---

## Nyugta PDF letöltése

Egy nyugta PDF fájljának letöltése és mentése a Laravel Storage-be.

```php
use NETipar\Szamlazzhu\Client;
use Illuminate\Support\Facades\Storage;

$client = app(Client::class);
$result = $client->getReceiptPdf('NYGTA-2026-1');
$pdf = $result->toPdf();

if ($pdf) {
    Storage::put('receipts/NYGTA-2026-1.pdf', $pdf);
}
```
