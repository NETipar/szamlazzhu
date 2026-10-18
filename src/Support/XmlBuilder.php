<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Support;

use DOMDocument;
use NETipar\Szamlazzhu\Exceptions\SzamlazzhuException;
use NETipar\Szamlazzhu\Exceptions\XmlBuildException;
use SimpleXMLElement;

class XmlBuilder
{
    public const string XML_BASE_URL = 'http://www.szamlazz.hu/';

    public const string XML_SCHEMA_CREATE_INVOICE = 'xmlszamla';

    public const string XML_SCHEMA_CREATE_REVERSE_INVOICE = 'xmlszamlast';

    public const string XML_SCHEMA_PAY_INVOICE = 'xmlszamlakifiz';

    public const string XML_SCHEMA_REQUEST_INVOICE_XML = 'xmlszamlaxml';

    public const string XML_SCHEMA_REQUEST_INVOICE_PDF = 'xmlszamlapdf';

    public const string XML_SCHEMA_CREATE_RECEIPT = 'xmlnyugtacreate';

    public const string XML_SCHEMA_CREATE_REVERSE_RECEIPT = 'xmlnyugtast';

    public const string XML_SCHEMA_SEND_RECEIPT = 'xmlnyugtasend';

    public const string XML_SCHEMA_GET_RECEIPT = 'xmlnyugtaget';

    public const string XML_SCHEMA_TAXPAYER = 'xmltaxpayer';

    public const string XML_SCHEMA_DELETE_PROFORMA = 'xmlszamladbkdel';

    /**
     * @return array<string, array{schema: string, fileName: string, xsdDir: string}>
     */
    public static function getSchemaMapping(): array
    {
        return [
            'generateProforma' => [
                'schema' => self::XML_SCHEMA_CREATE_INVOICE,
                'fileName' => 'action-xmlagentxmlfile',
                'xsdDir' => 'agent',
            ],
            'generateInvoice' => [
                'schema' => self::XML_SCHEMA_CREATE_INVOICE,
                'fileName' => 'action-xmlagentxmlfile',
                'xsdDir' => 'agent',
            ],
            'generatePrePaymentInvoice' => [
                'schema' => self::XML_SCHEMA_CREATE_INVOICE,
                'fileName' => 'action-xmlagentxmlfile',
                'xsdDir' => 'agent',
            ],
            'generateFinalInvoice' => [
                'schema' => self::XML_SCHEMA_CREATE_INVOICE,
                'fileName' => 'action-xmlagentxmlfile',
                'xsdDir' => 'agent',
            ],
            'generateCorrectiveInvoice' => [
                'schema' => self::XML_SCHEMA_CREATE_INVOICE,
                'fileName' => 'action-xmlagentxmlfile',
                'xsdDir' => 'agent',
            ],
            'generateDeliveryNote' => [
                'schema' => self::XML_SCHEMA_CREATE_INVOICE,
                'fileName' => 'action-xmlagentxmlfile',
                'xsdDir' => 'agent',
            ],
            'generateReverseInvoice' => [
                'schema' => self::XML_SCHEMA_CREATE_REVERSE_INVOICE,
                'fileName' => 'action-szamla_agent_st',
                'xsdDir' => 'agentst',
            ],
            'payInvoice' => [
                'schema' => self::XML_SCHEMA_PAY_INVOICE,
                'fileName' => 'action-szamla_agent_kifiz',
                'xsdDir' => 'agentkifiz',
            ],
            'requestInvoiceData' => [
                'schema' => self::XML_SCHEMA_REQUEST_INVOICE_XML,
                'fileName' => 'action-szamla_agent_xml',
                'xsdDir' => 'agentxml',
            ],
            'requestInvoicePDF' => [
                'schema' => self::XML_SCHEMA_REQUEST_INVOICE_PDF,
                'fileName' => 'action-szamla_agent_pdf',
                'xsdDir' => 'agentpdf',
            ],
            'generateReceipt' => [
                'schema' => self::XML_SCHEMA_CREATE_RECEIPT,
                'fileName' => 'action-szamla_agent_nyugta_create',
                'xsdDir' => 'nyugtacreate',
            ],
            'generateReverseReceipt' => [
                'schema' => self::XML_SCHEMA_CREATE_REVERSE_RECEIPT,
                'fileName' => 'action-szamla_agent_nyugta_storno',
                'xsdDir' => 'nyugtast',
            ],
            'sendReceipt' => [
                'schema' => self::XML_SCHEMA_SEND_RECEIPT,
                'fileName' => 'action-szamla_agent_nyugta_send',
                'xsdDir' => 'nyugtasend',
            ],
            'requestReceiptData' => [
                'schema' => self::XML_SCHEMA_GET_RECEIPT,
                'fileName' => 'action-szamla_agent_nyugta_get',
                'xsdDir' => 'nyugtaget',
            ],
            'requestReceiptPDF' => [
                'schema' => self::XML_SCHEMA_GET_RECEIPT,
                'fileName' => 'action-szamla_agent_nyugta_get',
                'xsdDir' => 'nyugtaget',
            ],
            'getTaxPayer' => [
                'schema' => self::XML_SCHEMA_TAXPAYER,
                'fileName' => 'action-szamla_agent_taxpayer',
                'xsdDir' => 'taxpayer',
            ],
            'deleteProforma' => [
                'schema' => self::XML_SCHEMA_DELETE_PROFORMA,
                'fileName' => 'action-szamla_agent_dijbekero_torlese',
                'xsdDir' => 'dijbekerodel',
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function buildXml(string $rootName, array $data, string $xmlNs, string $schemaLocation): string
    {
        $xmlBase = $this->getXmlBase($rootName, $xmlNs, $schemaLocation);
        $xml = new SimpleXmlExtended($xmlBase);

        $this->arrayToXml($data, $xml);

        $formatted = $this->formatXml($xml)->saveXML();

        if ($formatted === false) {
            throw new XmlBuildException(SzamlazzhuException::XML_DATA_BUILD_FAILED);
        }

        return $formatted;
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    public function arrayToXml(array $data, SimpleXmlExtended &$xml): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $subNode = $xml->addChild($this->resolveElementName((string) $key));
                $this->arrayToXml($value, $subNode);
            } else {
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }

                $xml->addChildWithCData((string) $key, (string) $value);
            }
        }
    }

    /**
     * Numbered collection keys (item0, note0, ...) come from the document builders
     * and must be renamed to their repeating XML element names.
     */
    private function resolveElementName(string $key): string
    {
        if (preg_match('/^item\d+$/', $key) === 1) {
            return 'tetel';
        }

        if (preg_match('/^note\d+$/', $key) === 1) {
            return 'kifizetes';
        }

        return $key;
    }

    public function formatXml(SimpleXMLElement $xml): DOMDocument
    {
        $xmlDocument = new DOMDocument('1.0');
        $xmlDocument->preserveWhiteSpace = false;
        $xmlDocument->formatOutput = true;

        $source = $xml->asXML();

        if ($source === false) {
            throw new XmlBuildException(SzamlazzhuException::XML_DATA_BUILD_FAILED);
        }

        $xmlDocument->loadXML($source);

        return $xmlDocument;
    }

    public function getXmlBase(string $xmlName, string $xmlNs, string $schemaLocation): string
    {
        $queryData = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $queryData .= "<{$xmlName} xmlns=\"{$xmlNs}\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"{$schemaLocation}\">"."\n";
        $queryData .= "</{$xmlName}>"."\n";

        return $queryData;
    }

    public static function getXmlNs(string $xmlName): string
    {
        return self::XML_BASE_URL.$xmlName;
    }

    public static function getSchemaLocation(string $xmlName, string $xsdDir): string
    {
        return self::XML_BASE_URL."szamla/{$xmlName} http://www.szamlazz.hu/szamla/docs/xsds/{$xsdDir}/{$xmlName}.xsd";
    }
}
