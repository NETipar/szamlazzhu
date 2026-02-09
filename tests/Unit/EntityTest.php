<?php

use NETipar\Szamlazzhu\Entity\Buyer;
use NETipar\Szamlazzhu\Entity\Seller;
use NETipar\Szamlazzhu\Entity\TaxPayer;

it('creates a buyer with xml array', function () {
    $buyer = new Buyer;
    $buyer->setName('Test Buyer')
        ->setCountry('Magyarország')
        ->setZipCode('1234')
        ->setCity('Budapest')
        ->setAddress('Test utca 1.')
        ->setEmail('test@example.com')
        ->setTaxNumber('12345678-1-42');

    $xml = $buyer->toXmlArray();

    expect($xml['nev'])->toBe('Test Buyer')
        ->and($xml['orszag'])->toBe('Magyarország')
        ->and($xml['irsz'])->toBe('1234')
        ->and($xml['telepules'])->toBe('Budapest')
        ->and($xml['cim'])->toBe('Test utca 1.')
        ->and($xml['email'])->toBe('test@example.com')
        ->and($xml['adoszam'])->toBe('12345678-1-42');
});

it('buyer xml array excludes null optional fields', function () {
    $buyer = new Buyer;
    $buyer->setName('Minimal Buyer')
        ->setZipCode('1000')
        ->setCity('Budapest')
        ->setAddress('Fő utca 1.');

    $xml = $buyer->toXmlArray();

    expect($xml)->not->toHaveKey('email')
        ->and($xml)->not->toHaveKey('adoszam')
        ->and($xml)->not->toHaveKey('postazasiNev');
});

it('creates a seller with xml array', function () {
    $seller = new Seller;
    $seller->setBankName('OTP Bank')
        ->setBankAccountNumber('11111111-22222222-33333333')
        ->setEmailReplyTo('info@test.com');

    $xml = $seller->toXmlArray();

    expect($xml['bank'])->toBe('OTP Bank')
        ->and($xml['bankszamlaszam'])->toBe('11111111-22222222-33333333')
        ->and($xml['emailReplyto'])->toBe('info@test.com');
});

it('seller xml array excludes null fields', function () {
    $seller = new Seller;
    $xml = $seller->toXmlArray();

    expect($xml)->toBe([]);
});

it('creates a tax payer with truncated id', function () {
    $taxPayer = new TaxPayer('12345678-1-42');

    expect($taxPayer->taxPayerId)->toBe('12345678');
});

it('tax payer generates correct xml array', function () {
    $taxPayer = new TaxPayer('12345678');
    $xml = $taxPayer->toXmlArray();

    expect($xml)->toBe(['torzsszam' => '12345678']);
});

it('buyer supports fluent setters', function () {
    $buyer = new Buyer;
    $result = $buyer->setName('Test')->setCity('Budapest');

    expect($result)->toBeInstanceOf(Buyer::class);
});

it('buyer supports postal address fields', function () {
    $buyer = new Buyer;
    $buyer->setPostalName('Postal Name')
        ->setPostalCountry('HU')
        ->setPostalZipCode('2000')
        ->setPostalCity('Szentendre')
        ->setPostalAddress('Postal utca 2.');

    $xml = $buyer->toXmlArray();

    expect($xml['postazasiNev'])->toBe('Postal Name')
        ->and($xml['postazasiOrszag'])->toBe('HU')
        ->and($xml['postazasiIrsz'])->toBe('2000')
        ->and($xml['postazasiTelepules'])->toBe('Szentendre')
        ->and($xml['postazasiCim'])->toBe('Postal utca 2.');
});
