# Adózó adatainak lekérdezése

A `getTaxPayer()` metódus segítségével lekérdezhetők egy adózó NAV-nál nyilvántartott adatai a szamlazz.hu API-n keresztül.

## Adószám és törzsszám

A lekérdezés paraméterként az adószám első 8 számjegyét (törzsszám) kell megadni. Például ha az adószám `12345678-2-42`, akkor a törzsszám `12345678`.

A csomag automatikusan levágja az első 8 karaktert, így akár a teljes adószámot is megadhatjuk.

## Alapvető használat

```php
use NETipar\Szamlazzhu\Client;

$client = app(Client::class);

// Törzsszám megadása (adószám első 8 számjegye)
$result = $client->getTaxPayer('12345678');

if ($result->isSuccess()) {
    $data = $result->toArray();
    // NAV adóalany adatok feldolgozása
}
```

## Válasz feldolgozása

Az `ApiResponse` objektumon keresztül több formátumban is elérhető a válasz.

### Tömb formátumban

```php
use NETipar\Szamlazzhu\Client;

$client = app(Client::class);
$result = $client->getTaxPayer('12345678');

if ($result->isSuccess()) {
    $data = $result->toArray();

    // Az adóalany adatai a 'result' kulcs alatt érhetők el
    $taxpayerInfo = $data['result'] ?? [];
}
```

### TaxPayerResponse objektumon keresztül

```php
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Response\TaxPayerResponse;

$client = app(Client::class);
$result = $client->getTaxPayer('12345678');

/** @var TaxPayerResponse|null $response */
$response = $result->getResponseObj();

if ($response !== null && $response->isSuccess()) {
    // Adóalany érvényes-e
    $isValid = $response->isTaxpayerValid();

    // Adóalany részletes adatai
    $taxpayerData = $response->getTaxPayerData();

    // Hibakód és hibaüzenet (ha van)
    $errorCode = $response->getErrorCode();
    $errorMessage = $response->getErrorMessage();
}
```

### XML és JSON formátumban

```php
use NETipar\Szamlazzhu\Client;

$client = app(Client::class);
$result = $client->getTaxPayer('12345678');

// XML válasz
$xml = $result->toXml();

// JSON válasz
$json = $result->toJson();

// Nyers NAV XML válasz
$rawXml = $result->getTaxPayerData();
```

## Hibakezelés

```php
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Exceptions\SzamlazzhuException;
use NETipar\Szamlazzhu\Exceptions\ConnectionException;
use NETipar\Szamlazzhu\Exceptions\ResponseException;

$client = app(Client::class);

try {
    $result = $client->getTaxPayer('12345678');

    if ($result->isSuccess()) {
        $data = $result->toArray();
    }
} catch (ConnectionException $e) {
    // Kapcsolódási hiba az API felé
} catch (ResponseException $e) {
    // API válasz hiba (pl. érvénytelen törzsszám)
} catch (SzamlazzhuException $e) {
    // Általános csomag hiba
}
```

## Megjegyzések

- A lekérdezés a NAV online számla rendszerében ellenőrzi az adózó adatait.
- A törzsszám kötelezően 8 számjegy. Ha hosszabb értéket adunk meg, a csomag automatikusan az első 8 karaktert használja.
- A válasz tartalmazza az adóalany érvényes/érvénytelen státuszát, valamint a nyilvántartott adatokat (név, cím, stb.).
