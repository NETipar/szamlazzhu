# Szamlazz.hu

This repository is a Laravel package. Keep the package focused, idiomatic, and easy for Laravel developers to install, test, and maintain.

## Package Conventions

- Use Laravel-native package APIs and the existing service provider shape before adding abstractions.
- Keep package names, namespaces, Composer metadata, publish tags, documentation, and examples aligned with `netipar/szamlazzhu`.
- Add only the files and dependencies needed for the package behavior being implemented.
- Prefer explicit Laravel package code over helper abstractions unless the extension point is real.
- Keep tests focused on observable package behavior through public APIs, service provider wiring, commands, routes, published resources, and documentation promises.

## Szamlazz.hu Specifics

- The package ports the official szamlazz.hu PHP SDK (`kboss/szamlaagent_v2`, [Kboss-hu-Kft/agentapi_v2](https://github.com/Kboss-hu-Kft/agentapi_v2)); the ported SDK version is stated in `README.md`. Port new SDK versions from the SDK source diff and its [changelog](https://docs.szamlazz.hu/hu/php/changelog), and check new XML elements against the published XSDs.
- The szamlazz.hu XSDs mandate the XML element order: keep the key order of `toXmlArray()` and of the settings builders in `Client`. `tests/Feature/RequestWireFormatTest.php` guards the request wire format and must stay green.
- Public API names are English (`setPaymentMethod()`), the emitted XML keys keep the Hungarian schema names (`fizmod`). Exception messages mirror the SDK's Hungarian messages as constants on `SzamlazzhuException`.

## Quick Commands

- Full validation: `composer test`
- Formatting check: `composer lint:check`
- Static analysis: `composer analyse`
- Pest tests: `composer test:unit`
- Workbench build: `composer build`
- Workbench server: `composer serve`

## Local Skills

- `package-scaffold`: use when adding package capabilities or wiring them through the service provider, including commands, migrations, routes, config, views, translations, assets, middleware, publish tags, workbench files, and console-only behavior.
- `package-testing`: use when adding or changing package tests with Pest 4/5 and Orchestra Testbench.
- `package-release`: use when preparing changelog, release notes, tags, or GitHub release workflow changes.
- `package-compatibility`: use when reviewing code, dependencies, or CI against the PHP and Laravel support matrix.
- `package-generate-skill`: use when updating the bundled Boost skill from the package implementation, README, and examples.
