# Changelog

All notable changes to `netipar/szamlazzhu` will be documented in this file.

## v1.0.1 - 2026-02-10

### Added
- GitHub Actions CI workflow for automated testing (PHP 8.1–8.4, Laravel 10–12)
- Laravel Pint code style enforcement with Laravel framework preset
- Tests badge in README

## v1.0.0 - 2026-02-10

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
