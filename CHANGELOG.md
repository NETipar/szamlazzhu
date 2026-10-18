# Release Notes

## [Unreleased](https://github.com/NETipar/szamlazzhu/compare/v3.0.0...HEAD)

## [v3.0.0](https://github.com/NETipar/szamlazzhu/compare/v2.0.1...v3.0.0) - 2026-10-18

Ported the official szamlazz.hu PHP SDK changes v2.12.3 → v2.12.5 and rebuilt the package tooling on the `laravel package` skeleton.

### Changed (breaking)

- Laravel 10 and 11 are no longer supported; the package requires Laravel 12 or 13 (PHP 8.3+ is unchanged). Action: on Laravel 10/11 stay on `^2.0`, or upgrade Laravel first.
- Class constants are typed (for example `public const string SYSTEM_DOWN`). Action: only if you extend a package class and override one of its constants, give your constant the same type.
- The cached szamlazz.hu session is keyed by the sha256 hash of the API key instead of md5. Action: none; the first request after the upgrade opens a new session. With the `database` session driver the old rows stay in the sessions table and can be deleted.

### Added

- `InvoiceHeader::setSimpleItems()`: simplified items on the invoice image (`simpleItems`, SDK 2.12.4). Delivery notes and corrective invoices throw a `ValidationException` before the request is sent.
- `php artisan vendor:publish --tag=szamlazzhu` publishes the config and the sessions migration together; the migration is published with a fresh timestamp and has a `down()` method.

### Deprecated

- `InvoiceHeader::setProfitVat()` (`arresAfa`) and `Item::setPriceGapVatBase()` (`arresAfaAlap`): the official SDK removes them after 2027-02-15 (SDK 2.12.5). They still work; on PHP 8.4+ calling them triggers a deprecation notice.

### Fixed

- A response that cannot be converted to JSON now throws `ResponseException` instead of a PHP `TypeError`.

## [v2.0.1](https://github.com/NETipar/szamlazzhu/compare/v2.0.0...v2.0.1) - 2026-07-06

### Fixed

- `DatabaseSessionStoreTest` now forces the in-memory sqlite connection so the suite runs on Testbench 8 / Laravel 10 (its mysql default broke the CI leg); no runtime code changes

## [v2.0.0](https://github.com/NETipar/szamlazzhu/compare/v1.0.2...v2.0.0) - 2026-07-06

Ported the official szamlazz.hu PHP SDK changes v2.10.23 → v2.12.3 and added Laravel 13 support.

### Changed (breaking)

- Minimum PHP version raised from 8.2 to **8.3**
- Receipt header XML now emits `devizaarf` before `devizabank` (matches the `xmlnyugtacreate` XSD sequence; previous order violated the schema when both fields were set)

### Added

- **Laravel 13 support** (`illuminate/http`/`illuminate/support` `^13.0`, Orchestra Testbench 11, Pest 4); CI matrix now covers PHP 8.3–8.4 × Laravel 10–13
- `Language` enum: `Bulgarian` (`bg`), `Dutch` (`nl`), `Russian` (`ru`), `Slovenian` (`si`) — SDK 2.11.00
- Receipt creation with order number: `ReceiptHeader::setOrderNumber()` emits `rendelesSzam` — SDK 2.12.1
- Receipt lookup by order number: `getReceiptData()`/`getReceiptPdf()` accept a `LookupType` parameter — SDK 2.12.2
- Invoice data/PDF lookup by external invoice id: `LookupType::ExternalId` now sends `szamlaKulsoAzon`; blank external id throws `ValidationException` — SDK 2.12.3
- `LookupType::DocumentNumber` constant as a readable alias of `InvoiceNumber` for receipt lookups
- Publishable migration for the `database` session driver (`php artisan vendor:publish --tag=szamlazzhu-migrations`)
- `SZAMLAZZHU_SESSION_DISK` and `SZAMLAZZHU_SESSION_TABLE` env variables
- `composer test` and `composer lint` scripts

### Fixed

- Session config keys `session.cache_store`/`session.cache_prefix` were silently ignored by `SessionManager` (it read undocumented `store`/`prefix` keys); documented keys now win with legacy fallback
- The `SessionStore` container binding used a divergent driver factory; it now delegates to `SessionManager`
- Custom headers added via `Client::addCustomHeader()` were never sent with requests
- Malformed response bodies threw a raw `Exception` from `SimpleXMLElement`; they now throw `ResponseException`
- Namespace stripping in taxpayer responses no longer collapses whitespace inside text values
- `XmlBuilder` element renaming now only matches generated `item{N}`/`note{N}` collection keys instead of any key containing `item`/`note`
- Missing `declare(strict_types=1)` in the `Szamlazzhu` facade

### Internal

- `Client::buildSettings()` split into per-request-type builders (XSD-mandated element order documented in code)
- Unreachable reverse-document branches removed from `Client::buildXmlData()`; `Invoice::toReverseXmlArray()` and `Receipt::toReverseXmlArray()` are deprecated
- `ApiRequest` is now created via the injectable `ApiRequestFactory`
- Wire-format regression tests guard the settings element order

## [v1.0.2](https://github.com/NETipar/szamlazzhu/compare/v1.0.1...v1.0.2) - 2026-02-10

### Fixed

- Added `guzzlehttp/guzzle` as explicit dependency (required for Laravel 10 where `illuminate/http` only suggests it)
- Added `phpunit.xml.dist` for proper PHPUnit/Pest configuration in CI
- Fixed CI workflow: dropped PHP 8.1 from test matrix (Pint and Pest require 8.2+), separated Pint into dedicated job

## [v1.0.1](https://github.com/NETipar/szamlazzhu/compare/v1.0.0...v1.0.1) - 2026-02-10

### Added

- GitHub Actions CI workflow for automated testing (PHP 8.1–8.4, Laravel 10–12)
- Laravel Pint code style enforcement with Laravel framework preset
- Tests badge in README

## [v1.0.0](https://github.com/NETipar/szamlazzhu/releases/tag/v1.0.0) - 2026-02-10

Initial release based on the official szamlazz.hu PHP SDK v2.10.23.

### Features

- Generate invoices, pre-payment invoices, final invoices, and corrective invoices
- Generate receipts
- Generate proforma invoices and delivery notes
- Reverse (storno) invoices and receipts
- Record payments on invoices
- Query invoice and receipt data
- Download invoice and receipt PDFs
- Query taxpayer data (NAV)
- Delete proforma invoices

### Architecture

- PHP 8.1+ typed properties, enums, constructor promotion
- Laravel HTTP Client (replaces cURL)
- Laravel Storage facade for PDF/XML persistence
- Laravel Log facade for logging
- Driver-based session management (cache, file, database, null)
- Service Container singleton binding
- Full enum support: `PaymentMethod`, `Currency`, `Language`, `VatRate`, `LookupType`, `DocumentType`
- Structured exception hierarchy
- 84 tests, 242 assertions
