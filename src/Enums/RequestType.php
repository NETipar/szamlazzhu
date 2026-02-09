<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Enums;

enum RequestType: string
{
    case GenerateInvoice = 'generateInvoice';
    case GeneratePrePaymentInvoice = 'generatePrePaymentInvoice';
    case GenerateFinalInvoice = 'generateFinalInvoice';
    case GenerateCorrectiveInvoice = 'generateCorrectiveInvoice';
    case GenerateReverseInvoice = 'generateReverseInvoice';
    case GenerateProforma = 'generateProforma';
    case GenerateDeliveryNote = 'generateDeliveryNote';
    case GenerateReceipt = 'generateReceipt';
    case GenerateReverseReceipt = 'generateReverseReceipt';
    case PayInvoice = 'payInvoice';
    case SendReceipt = 'sendReceipt';
    case RequestInvoiceData = 'requestInvoiceData';
    case RequestInvoicePdf = 'requestInvoicePDF';
    case RequestReceiptData = 'requestReceiptData';
    case RequestReceiptPdf = 'requestReceiptPDF';
    case GetTaxPayer = 'getTaxPayer';
    case DeleteProforma = 'deleteProforma';

    public function isInvoiceCreation(): bool
    {
        return in_array($this, [
            self::GenerateInvoice,
            self::GeneratePrePaymentInvoice,
            self::GenerateFinalInvoice,
            self::GenerateCorrectiveInvoice,
            self::GenerateProforma,
            self::GenerateDeliveryNote,
        ]);
    }

    public function isReceiptType(): bool
    {
        return in_array($this, [
            self::GenerateReceipt,
            self::GenerateReverseReceipt,
            self::RequestReceiptData,
            self::RequestReceiptPdf,
        ]);
    }
}
