# Taxpayer Query

Using the `getTaxPayer()` method, you can query the data of a taxpayer registered with the Hungarian Tax Authority (NAV) through the szamlazz.hu API.

## Tax Number and Tax ID

The query requires the first 8 digits of the tax number (tax ID) as a parameter. For example, if the tax number is `12345678-2-42`, then the tax ID is `12345678`.

The package automatically trims to the first 8 characters, so you can also provide the full tax number.

## Basic Usage

```php
use NETipar\Szamlazzhu\Client;

$client = app(Client::class);

// Provide the tax ID (first 8 digits of the tax number)
$result = $client->getTaxPayer('12345678');

if ($result->isSuccess()) {
    $data = $result->toArray();
    // Process NAV taxpayer data
}
```

## Response Processing

The response is available in multiple formats through the `ApiResponse` object.

### Array Format

```php
use NETipar\Szamlazzhu\Client;

$client = app(Client::class);
$result = $client->getTaxPayer('12345678');

if ($result->isSuccess()) {
    $data = $result->toArray();

    // Taxpayer data is available under the 'result' key
    $taxpayerInfo = $data['result'] ?? [];
}
```

### Via TaxPayerResponse Object

```php
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Response\TaxPayerResponse;

$client = app(Client::class);
$result = $client->getTaxPayer('12345678');

/** @var TaxPayerResponse|null $response */
$response = $result->getResponseObj();

if ($response !== null && $response->isSuccess()) {
    // Whether the taxpayer is valid
    $isValid = $response->isTaxpayerValid();

    // Detailed taxpayer data
    $taxpayerData = $response->getTaxPayerData();

    // Error code and error message (if any)
    $errorCode = $response->getErrorCode();
    $errorMessage = $response->getErrorMessage();
}
```

### XML and JSON Format

```php
use NETipar\Szamlazzhu\Client;

$client = app(Client::class);
$result = $client->getTaxPayer('12345678');

// XML response
$xml = $result->toXml();

// JSON response
$json = $result->toJson();

// Raw NAV XML response
$rawXml = $result->getTaxPayerData();
```

## Error Handling

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
    // Connection error towards the API
} catch (ResponseException $e) {
    // API response error (e.g., invalid tax ID)
} catch (SzamlazzhuException $e) {
    // General package error
}
```

## Notes

- The query verifies the taxpayer's data in the NAV online invoicing system.
- The tax ID must be exactly 8 digits. If a longer value is provided, the package automatically uses the first 8 characters.
- The response contains the taxpayer's valid/invalid status, as well as the registered data (name, address, etc.).
