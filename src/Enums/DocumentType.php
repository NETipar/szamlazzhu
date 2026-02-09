<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Enums;

enum DocumentType: string
{
    case Invoice = 'invoice';
    case ReverseInvoice = 'reverseInvoice';
    case PayInvoice = 'payInvoice';
    case CorrectiveInvoice = 'correctiveInvoice';
    case PrepaymentInvoice = 'prePaymentInvoice';
    case FinalInvoice = 'finalInvoice';
    case Proforma = 'proforma';
    case DeliveryNote = 'deliveryNote';
    case Receipt = 'receipt';
    case ReverseReceipt = 'reverseReceipt';

    public function code(): string
    {
        return match ($this) {
            self::Invoice => 'SZ',
            self::ReverseInvoice => 'SS',
            self::PayInvoice => 'JS',
            self::CorrectiveInvoice => 'HS',
            self::PrepaymentInvoice => 'ES',
            self::FinalInvoice => 'VS',
            self::Proforma => 'D',
            self::DeliveryNote => 'SL',
            self::Receipt => 'NY',
            self::ReverseReceipt => 'SN',
        };
    }

    public function isInvoiceFamily(): bool
    {
        return in_array($this, [
            self::Invoice,
            self::ReverseInvoice,
            self::CorrectiveInvoice,
            self::PrepaymentInvoice,
            self::FinalInvoice,
            self::Proforma,
            self::DeliveryNote,
        ]);
    }

    public function isReceiptFamily(): bool
    {
        return in_array($this, [
            self::Receipt,
            self::ReverseReceipt,
        ]);
    }
}
