# Telepítés és konfiguráció

## 1. Telepítés

A csomag telepítése Composer segítségével:

```bash
composer require netipar/szamlazzhu
```

A csomag Laravel 10, 11 és 12 verziókon támogatott, PHP 8.1 vagy újabb szükséges.

A ServiceProvider automatikusan regisztrálódik a Laravel package discovery rendszeren keresztül.

## 2. Konfiguráció publikálása

A konfigurációs fájl publikálása:

```bash
php artisan vendor:publish --tag=szamlazzhu-config
```

Ez létrehozza a `config/szamlazzhu.php` fájlt.

## 3. Környezeti változók (.env)

Add hozzá az alábbi környezeti változókat a `.env` fájlhoz:

```
SZAMLAZZHU_API_KEY=your-api-key-here
SZAMLAZZHU_DOWNLOAD_PDF=true
SZAMLAZZHU_RESPONSE_TYPE=1
SZAMLAZZHU_TIMEOUT=30
SZAMLAZZHU_STORAGE_DISK=local
SZAMLAZZHU_SAVE_PDF=true
SZAMLAZZHU_SESSION_DRIVER=cache
SZAMLAZZHU_LOG_CHANNEL=null
```

| Változó | Leírás | Alapérték |
|---|---|---|
| `SZAMLAZZHU_API_KEY` | Szamlazz.hu API kulcs (Számla Agent kulcs) | `''` |
| `SZAMLAZZHU_DOWNLOAD_PDF` | PDF letöltése a válasszal együtt | `true` |
| `SZAMLAZZHU_RESPONSE_TYPE` | Válasz típusa (1 = szöveges, 2 = XML) | `1` |
| `SZAMLAZZHU_TIMEOUT` | Kérés timeout másodpercben | `30` |
| `SZAMLAZZHU_STORAGE_DISK` | Laravel Storage disk neve a fájlok tárolásához | `local` |
| `SZAMLAZZHU_SAVE_PDF` | PDF automatikus mentése a tárolóra | `true` |
| `SZAMLAZZHU_SESSION_DRIVER` | Session driver (cache, file, database, null) | `cache` |
| `SZAMLAZZHU_LOG_CHANNEL` | Laravel log csatorna neve (null = nincs logolás) | `null` |

## 4. Konfigurációs fájl

A teljes konfigurációs fájl tartalma (`config/szamlazzhu.php`):

```php
<?php

return [
    'api_key' => env('SZAMLAZZHU_API_KEY', ''),
    'api_url' => env('SZAMLAZZHU_API_URL', 'https://www.szamlazz.hu/szamla/'),
    'download_pdf' => env('SZAMLAZZHU_DOWNLOAD_PDF', true),
    'response_type' => env('SZAMLAZZHU_RESPONSE_TYPE', 1),
    'timeout' => env('SZAMLAZZHU_TIMEOUT', 30),
    'connect_timeout' => env('SZAMLAZZHU_CONNECT_TIMEOUT', 0),
    'aggregator' => env('SZAMLAZZHU_AGGREGATOR', ''),
    'storage' => [
        'disk' => env('SZAMLAZZHU_STORAGE_DISK', 'local'),
        'pdf_path' => 'szamlazzhu/pdf',
        'xml_path' => 'szamlazzhu/xml',
    ],
    'save_pdf' => env('SZAMLAZZHU_SAVE_PDF', true),
    'save_request_xml' => env('SZAMLAZZHU_SAVE_REQUEST_XML', false),
    'save_response_xml' => env('SZAMLAZZHU_SAVE_RESPONSE_XML', false),
    'session' => [
        'driver' => env('SZAMLAZZHU_SESSION_DRIVER', 'cache'),
        'cache_store' => env('SZAMLAZZHU_SESSION_CACHE_STORE', null),
        'cache_prefix' => 'szamlazzhu_session_',
        'ttl' => 3600,
    ],
    'log_channel' => env('SZAMLAZZHU_LOG_CHANNEL', null),
    'certification_path' => env('SZAMLAZZHU_CERT_PATH', null),
];
```

### Konfigurációs beállítások részletezése

- **api_key**: A szamlazz.hu felületen generált Számla Agent kulcs.
- **api_url**: Az API végpont címe. Általában nem szükséges módosítani.
- **download_pdf**: Ha `true`, a generált bizonylat PDF-je is visszajön a válaszban.
- **response_type**: A válasz formátuma. `1` = szöveges, `2` = XML.
- **timeout / connect_timeout**: HTTP kérés timeout értékek másodpercben.
- **aggregator**: Szamlazz.hu aggregátor azonosító (ha van).
- **storage**: A mentett fájlok (PDF, XML) tárolásának beállításai.
- **save_pdf**: PDF automatikus mentése a megadott Storage disk-re.
- **save_request_xml / save_response_xml**: Debug célra hasznos XML mentés.
- **session**: A szamlazz.hu session kezelésnek beállításai.
- **log_channel**: Laravel log csatorna. Ha `null`, nincs logolás.
- **certification_path**: E-számla tanúsítvány fájl elérési útja (ha használt).

## 5. Használat

A `Client` osztályt Dependency Injection-nel vagy a Service Container-en keresztül érheted el:

### Dependency Injection

```php
use NETipar\Szamlazzhu\Client;

class InvoiceController extends Controller
{
    public function store(Client $client): void
    {
        $result = $client->generateInvoice($invoice);
    }
}
```

### Service Container

```php
use NETipar\Szamlazzhu\Client;

$client = app(Client::class);
$result = $client->generateInvoice($invoice);
```

### Artisan parancsban

```php
use NETipar\Szamlazzhu\Client;

class GenerateInvoiceCommand extends Command
{
    protected $signature = 'invoice:generate';

    public function handle(Client $client): void
    {
        $result = $client->generateInvoice($invoice);

        $this->info("Számla generálva: {$result->getDocumentNumber()}");
    }
}
```

## 6. Session kezelés

A csomag a szamlazz.hu API session-jeit kezeli a különböző driver-eken keresztül. A session driver a `session.driver` konfigurációs értékkel állítható be.

| Driver | Leírás | Használat |
|---|---|---|
| `cache` | Laravel Cache-ben tárolja a session-t | **Alapértelmezett.** Bármilyen cache store-ral működik (Redis, Memcached, file, stb.). |
| `file` | Fájlrendszerben tárolja a session-t | Egyszerűbb környezetekhez, ahol nincs cache konfiguráció. |
| `database` | Adatbázisban tárolja a session-t | Ha adatbázis szintű perzisztencia szükséges. |
| `null` | Nem tárol session-t | Teszteléshez és fejlesztéshez. |

### Cache driver beállítása

```
SZAMLAZZHU_SESSION_DRIVER=cache
SZAMLAZZHU_SESSION_CACHE_STORE=redis
```

Ha a `cache_store` nincs megadva (`null`), az alkalmazás alapértelmezett cache store-ját használja.

## 7. Hibakezelés

A csomag különböző kivételeket dob az egyes hibatípusokhoz. Minden kivétel a `SzamlazzhuException` osztályból származik.

```php
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Exceptions\SzamlazzhuException;
use NETipar\Szamlazzhu\Exceptions\ConnectionException;
use NETipar\Szamlazzhu\Exceptions\ResponseException;
use NETipar\Szamlazzhu\Exceptions\ValidationException;
use NETipar\Szamlazzhu\Exceptions\XmlBuildException;

$client = app(Client::class);

try {
    $result = $client->generateInvoice($invoice);
} catch (ConnectionException $e) {
    // Kapcsolódási hiba a szamlazz.hu API felé
    // Pl. timeout, hálózati hiba
} catch (ResponseException $e) {
    // API válasz hiba
    // Pl. érvénytelen adatok, API szintű elutasítás
} catch (ValidationException $e) {
    // Validációs hiba
    // Pl. hiányzó kötelező mező
} catch (XmlBuildException $e) {
    // XML építési hiba
    // Pl. hibás XML struktúra
} catch (SzamlazzhuException $e) {
    // Általános csomag hiba (minden fenti kivétel szülője)
}
```

### Kivétel hierarchia

```
SzamlazzhuException (alap)
  |- ConnectionException    -- Hálózati / kapcsolódási hibák
  |- ResponseException      -- API válasz feldolgozási hibák
  |- ValidationException    -- Bemeneti adat validációs hibák
  |- XmlBuildException      -- XML generálási hibák
```

### Válasz hibakód lekérdezése

```php
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Exceptions\SzamlazzhuException;

$client = app(Client::class);

try {
    $result = $client->generateInvoice($invoice);
} catch (SzamlazzhuException $e) {
    $errorMessage = $e->getMessage();
    $errorCode = $e->getCode();

    logger()->error("Szamlazzhu hiba: [{$errorCode}] {$errorMessage}");
}
```

Ha a kérés sikeres, de a válaszban hibakód található:

```php
$result = $client->generateInvoice($invoice);

if ($result->isFailed()) {
    $errorCode = $result->getErrorCode();
    $errorMsg = $result->getErrorMsg();
}
```
