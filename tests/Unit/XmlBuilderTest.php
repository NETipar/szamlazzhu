<?php

use NETipar\Szamlazzhu\Support\XmlBuilder;
use NETipar\Szamlazzhu\Support\XmlValidator;

it('builds valid xml from array data', function () {
    $builder = new XmlBuilder;

    $xmlNs = XmlBuilder::getXmlNs('xmlszamla');
    $schemaLocation = XmlBuilder::getSchemaLocation('xmlszamla', 'agent');

    $data = [
        'beallitasok' => [
            'szamlaagentkulcs' => 'test-key',
            'eszamla' => true,
            'szamlaLetoltes' => true,
        ],
        'fejlec' => [
            'keltDatum' => '2025-01-15',
            'fizmod' => 'átutalás',
        ],
    ];

    $xml = $builder->buildXml('xmlszamla', $data, $xmlNs, $schemaLocation);

    expect($xml)->toContain('<?xml version="1.0"')
        ->and($xml)->toContain('<xmlszamla')
        ->and($xml)->toContain('test-key')
        ->and($xml)->toContain('2025-01-15')
        ->and($xml)->toContain('átutalás');
});

it('has correct schema mapping for all request types', function () {
    $mapping = XmlBuilder::getSchemaMapping();

    expect($mapping)->toHaveKeys([
        'generateInvoice',
        'generatePrePaymentInvoice',
        'generateFinalInvoice',
        'generateCorrectiveInvoice',
        'generateDeliveryNote',
        'generateProforma',
        'generateReverseInvoice',
        'payInvoice',
        'requestInvoiceData',
        'requestInvoicePDF',
        'generateReceipt',
        'generateReverseReceipt',
        'sendReceipt',
        'requestReceiptData',
        'requestReceiptPDF',
        'getTaxPayer',
        'deleteProforma',
    ]);
});

it('generates correct xml namespace', function () {
    $ns = XmlBuilder::getXmlNs('xmlszamla');

    expect($ns)->toBe('http://www.szamlazz.hu/xmlszamla');
});

it('generates correct schema location', function () {
    $location = XmlBuilder::getSchemaLocation('xmlszamla', 'agent');

    expect($location)->toBe('http://www.szamlazz.hu/szamla/xmlszamla http://www.szamlazz.hu/szamla/docs/xsds/agent/xmlszamla.xsd');
});

it('converts boolean values to true/false strings', function () {
    $builder = new XmlBuilder;

    $xmlNs = XmlBuilder::getXmlNs('xmlszamla');
    $schemaLocation = XmlBuilder::getSchemaLocation('xmlszamla', 'agent');

    $data = [
        'beallitasok' => [
            'eszamla' => true,
            'szamlaLetoltes' => false,
        ],
    ];

    $xml = $builder->buildXml('xmlszamla', $data, $xmlNs, $schemaLocation);

    expect($xml)->toContain('true')
        ->and($xml)->toContain('false');
});

it('validates correct xml', function () {
    $validator = new XmlValidator;

    $xml = '<?xml version="1.0" encoding="UTF-8"?><root><item>test</item></root>';
    $errors = $validator->checkValidXml($xml);

    expect($errors)->toBe([]);
});

it('returns errors for invalid xml', function () {
    $validator = new XmlValidator;

    $xml = '<?xml version="1.0" encoding="UTF-8"?><root><unclosed>';
    $errors = $validator->checkValidXml($xml);

    expect($errors)->not->toBe([]);
});

it('invoice generation uses correct schema', function () {
    $mapping = XmlBuilder::getSchemaMapping();

    expect($mapping['generateInvoice']['schema'])->toBe(XmlBuilder::XML_SCHEMA_CREATE_INVOICE)
        ->and($mapping['generateInvoice']['fileName'])->toBe('action-xmlagentxmlfile')
        ->and($mapping['generateInvoice']['xsdDir'])->toBe('agent');
});

it('receipt generation uses correct schema', function () {
    $mapping = XmlBuilder::getSchemaMapping();

    expect($mapping['generateReceipt']['schema'])->toBe(XmlBuilder::XML_SCHEMA_CREATE_RECEIPT)
        ->and($mapping['generateReverseReceipt']['schema'])->toBe(XmlBuilder::XML_SCHEMA_CREATE_REVERSE_RECEIPT);
});

it('tax payer uses correct schema', function () {
    $mapping = XmlBuilder::getSchemaMapping();

    expect($mapping['getTaxPayer']['schema'])->toBe(XmlBuilder::XML_SCHEMA_TAXPAYER);
});
