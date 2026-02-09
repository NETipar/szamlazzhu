<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Header;

use NETipar\Szamlazzhu\Enums\Currency;
use NETipar\Szamlazzhu\Enums\DocumentType;
use NETipar\Szamlazzhu\Enums\PaymentMethod;

abstract class DocumentHeader
{
    abstract public function documentType(): DocumentType;

    abstract public function toXmlArray(): array;

    public function isInvoice(): bool
    {
        return $this->documentType()->isInvoiceFamily();
    }

    public function isReverseInvoice(): bool
    {
        return $this->documentType() === DocumentType::ReverseInvoice;
    }

    public function isPrePayment(): bool
    {
        return $this->documentType() === DocumentType::PrepaymentInvoice;
    }

    public function isFinal(): bool
    {
        return $this->documentType() === DocumentType::FinalInvoice;
    }

    public function isCorrective(): bool
    {
        return $this->documentType() === DocumentType::CorrectiveInvoice;
    }

    public function isProforma(): bool
    {
        return $this->documentType() === DocumentType::Proforma;
    }

    public function isDeliveryNote(): bool
    {
        return $this->documentType() === DocumentType::DeliveryNote;
    }

    public function isReceipt(): bool
    {
        return $this->documentType()->isReceiptFamily();
    }

    public function isReverseReceipt(): bool
    {
        return $this->documentType() === DocumentType::ReverseReceipt;
    }

    protected function resolvePaymentMethod(PaymentMethod|string|null $paymentMethod): string
    {
        if ($paymentMethod instanceof PaymentMethod) {
            return $paymentMethod->value;
        }

        return (string) $paymentMethod;
    }

    protected function resolveCurrency(Currency|string|null $currency): string
    {
        if ($currency instanceof Currency) {
            return $currency->value;
        }

        return (string) $currency;
    }
}
