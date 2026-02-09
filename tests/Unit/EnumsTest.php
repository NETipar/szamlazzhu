<?php

use NETipar\Szamlazzhu\Enums\Currency;
use NETipar\Szamlazzhu\Enums\Language;
use NETipar\Szamlazzhu\Enums\PaymentMethod;
use NETipar\Szamlazzhu\Enums\ResponseType;
use NETipar\Szamlazzhu\Enums\VatRate;

it('has correct PaymentMethod values', function () {
    expect(PaymentMethod::BankTransfer->value)->toBe('átutalás')
        ->and(PaymentMethod::Cash->value)->toBe('készpénz')
        ->and(PaymentMethod::CreditCard->value)->toBe('bankkártya');
});

it('has correct Currency values', function () {
    expect(Currency::HUF->value)->toBe('HUF')
        ->and(Currency::EUR->value)->toBe('EUR')
        ->and(Currency::USD->value)->toBe('USD')
        ->and(Currency::Ft->value)->toBe('Ft');
});

it('has currency labels', function () {
    expect(Currency::HUF->label())->toBe('forint')
        ->and(Currency::EUR->label())->toBe('euró')
        ->and(Currency::USD->label())->toBe('amerikai dollár');
});

it('has correct Language values', function () {
    expect(Language::Hungarian->value)->toBe('hu')
        ->and(Language::English->value)->toBe('en')
        ->and(Language::German->value)->toBe('de');
});

it('has correct ResponseType values', function () {
    expect(ResponseType::Text->value)->toBe(1)
        ->and(ResponseType::Xml->value)->toBe(2)
        ->and(ResponseType::TaxPayerXml->value)->toBe(3);
});

it('can create ResponseType from value', function () {
    expect(ResponseType::from(1))->toBe(ResponseType::Text)
        ->and(ResponseType::from(2))->toBe(ResponseType::Xml);
});

it('has correct VatRate values', function () {
    expect(VatRate::Percent27->value)->toBe('27')
        ->and(VatRate::Percent5->value)->toBe('5')
        ->and(VatRate::TAM->value)->toBe('TAM')
        ->and(VatRate::AAM->value)->toBe('AAM');
});
