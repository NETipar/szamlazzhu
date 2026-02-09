# Installation and Configuration

## 1. Installation

Install the package via Composer:

```bash
composer require netipar/szamlazzhu
```

The package supports Laravel 10, 11, and 12, and requires PHP 8.1 or later.

The ServiceProvider is automatically registered through Laravel's package discovery system.

## 2. Publishing the Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=szamlazzhu-config
```

This creates the `config/szamlazzhu.php` file.

## 3. Environment Variables (.env)

Add the following environment variables to your `.env` file:

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

| Variable | Description | Default |
|---|---|---|
| `SZAMLAZZHU_API_KEY` | Szamlazz.hu API key (Szamla Agent key) | `''` |
| `SZAMLAZZHU_DOWNLOAD_PDF` | Download PDF with the response | `true` |
| `SZAMLAZZHU_RESPONSE_TYPE` | Response type (1 = text, 2 = XML) | `1` |
| `SZAMLAZZHU_TIMEOUT` | Request timeout in seconds | `30` |
| `SZAMLAZZHU_STORAGE_DISK` | Laravel Storage disk name for file storage | `local` |
| `SZAMLAZZHU_SAVE_PDF` | Automatically save PDF to storage | `true` |
| `SZAMLAZZHU_SESSION_DRIVER` | Session driver (cache, file, database, null) | `cache` |
| `SZAMLAZZHU_LOG_CHANNEL` | Laravel log channel name (null = no logging) | `null` |

## 4. Configuration File

The full contents of the configuration file (`config/szamlazzhu.php`):

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

### Configuration Options Details

- **api_key**: The Szamla Agent key generated on the szamlazz.hu dashboard.
- **api_url**: The API endpoint URL. Usually does not need to be changed.
- **download_pdf**: If `true`, the generated document PDF is also included in the response.
- **response_type**: The response format. `1` = text, `2` = XML.
- **timeout / connect_timeout**: HTTP request timeout values in seconds.
- **aggregator**: Szamlazz.hu aggregator identifier (if applicable).
- **storage**: Storage settings for saved files (PDF, XML).
- **save_pdf**: Automatically save PDF to the specified Storage disk.
- **save_request_xml / save_response_xml**: Useful XML saving for debugging purposes.
- **session**: Settings for szamlazz.hu session management.
- **log_channel**: Laravel log channel. If `null`, no logging is performed.
- **certification_path**: Path to the e-invoice certificate file (if used).

## 5. Usage

You can access the `Client` class via Dependency Injection or through the Service Container:

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

### In an Artisan Command

```php
use NETipar\Szamlazzhu\Client;

class GenerateInvoiceCommand extends Command
{
    protected $signature = 'invoice:generate';

    public function handle(Client $client): void
    {
        $result = $client->generateInvoice($invoice);

        $this->info("Invoice generated: {$result->getDocumentNumber()}");
    }
}
```

## 6. Session Management

The package manages szamlazz.hu API sessions through various drivers. The session driver can be configured with the `session.driver` configuration value.

| Driver | Description | Usage |
|---|---|---|
| `cache` | Stores the session in Laravel Cache | **Default.** Works with any cache store (Redis, Memcached, file, etc.). |
| `file` | Stores the session in the filesystem | For simpler environments without cache configuration. |
| `database` | Stores the session in the database | When database-level persistence is required. |
| `null` | Does not store sessions | For testing and development. |

### Cache Driver Configuration

```
SZAMLAZZHU_SESSION_DRIVER=cache
SZAMLAZZHU_SESSION_CACHE_STORE=redis
```

If `cache_store` is not specified (`null`), the application's default cache store is used.

## 7. Error Handling

The package throws various exceptions for different error types. All exceptions extend the `SzamlazzhuException` class.

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
    // Connection error to the szamlazz.hu API
    // E.g. timeout, network error
} catch (ResponseException $e) {
    // API response error
    // E.g. invalid data, API-level rejection
} catch (ValidationException $e) {
    // Validation error
    // E.g. missing required field
} catch (XmlBuildException $e) {
    // XML build error
    // E.g. invalid XML structure
} catch (SzamlazzhuException $e) {
    // General package error (parent of all above exceptions)
}
```

### Exception Hierarchy

```
SzamlazzhuException (base)
  |- ConnectionException    -- Network / connection errors
  |- ResponseException      -- API response processing errors
  |- ValidationException    -- Input data validation errors
  |- XmlBuildException      -- XML generation errors
```

### Querying Response Error Codes

```php
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Exceptions\SzamlazzhuException;

$client = app(Client::class);

try {
    $result = $client->generateInvoice($invoice);
} catch (SzamlazzhuException $e) {
    $errorMessage = $e->getMessage();
    $errorCode = $e->getCode();

    logger()->error("Szamlazzhu error: [{$errorCode}] {$errorMessage}");
}
```

If the request succeeds but the response contains an error code:

```php
$result = $client->generateInvoice($invoice);

if ($result->isFailed()) {
    $errorCode = $result->getErrorCode();
    $errorMsg = $result->getErrorMsg();
}
```
