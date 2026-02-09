<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Header;

use Carbon\Carbon;
use NETipar\Szamlazzhu\Enums\Currency;
use NETipar\Szamlazzhu\Enums\DocumentType;
use NETipar\Szamlazzhu\Enums\InvoiceTemplate;
use NETipar\Szamlazzhu\Enums\Language;
use NETipar\Szamlazzhu\Enums\PaymentMethod;

class InvoiceHeader extends DocumentHeader
{
    public ?string $invoiceNumber = null;

    public Carbon|string|null $issueDate = null;

    public PaymentMethod|string|null $paymentMethod = null;

    public Currency|string|null $currency = null;

    public Language|string|null $language = null;

    public Carbon|string|null $fulfillment = null;

    public Carbon|string|null $paymentDue = null;

    public ?string $prefix = null;

    public ?string $extraLogo = null;

    public ?float $correctionToPay = null;

    public ?string $correctivedNumber = null;

    public ?string $comment = null;

    public ?string $exchangeBank = null;

    public ?float $exchangeRate = null;

    public ?string $orderNumber = null;

    public ?string $proformaNumber = null;

    public ?bool $paid = null;

    public ?bool $profitVat = null;

    public InvoiceTemplate|string|null $invoiceTemplate = null;

    public ?string $prePaymentInvoiceNumber = null;

    public ?bool $previewPdf = null;

    public ?bool $euVat = null;

    public function documentType(): DocumentType
    {
        return DocumentType::Invoice;
    }

    public function toXmlArray(): array
    {
        $data = [];

        if ($this->issueDate !== null) {
            $data['keltDatum'] = $this->resolveDate($this->issueDate);
        }

        if ($this->fulfillment !== null) {
            $data['teljesitesDatum'] = $this->resolveDate($this->fulfillment);
        }

        if ($this->paymentDue !== null) {
            $data['fizetesiHataridoDatum'] = $this->resolveDate($this->paymentDue);
        }

        if ($this->paymentMethod !== null) {
            $data['fizmod'] = $this->resolvePaymentMethod($this->paymentMethod);
        }

        if ($this->currency !== null) {
            $data['penznem'] = $this->resolveCurrency($this->currency);
        }

        if ($this->language !== null) {
            $data['szamlaNyelve'] = $this->resolveLanguage();
        }

        if ($this->comment !== null) {
            $data['megjegyzes'] = $this->comment;
        }

        if ($this->exchangeBank !== null) {
            $data['arfolyamBank'] = $this->exchangeBank;
        }

        if ($this->exchangeRate !== null) {
            $data['arfolyam'] = $this->exchangeRate;
        }

        if ($this->orderNumber !== null) {
            $data['rendelesSzam'] = $this->orderNumber;
        }

        if ($this->proformaNumber !== null) {
            $data['dijbekeroSzamlaszam'] = $this->proformaNumber;
        }

        if ($this->documentType() === DocumentType::PrepaymentInvoice) {
            $data['elolegszamla'] = true;
        }

        if ($this->documentType() === DocumentType::FinalInvoice) {
            $data['vegszamla'] = true;
        }

        if ($this->prePaymentInvoiceNumber !== null) {
            $data['elolegSzamlaszam'] = $this->prePaymentInvoiceNumber;
        }

        if ($this->documentType() === DocumentType::CorrectiveInvoice) {
            $data['helyesbitoszamla'] = true;
        }

        if ($this->correctivedNumber !== null) {
            $data['helyesbitettSzamlaszam'] = $this->correctivedNumber;
        }

        if ($this->documentType() === DocumentType::Proforma) {
            $data['dijbekero'] = true;
        }

        if ($this->documentType() === DocumentType::DeliveryNote) {
            $data['szallitolevel'] = true;
        }

        if ($this->extraLogo !== null) {
            $data['logoExtra'] = $this->extraLogo;
        }

        if ($this->prefix !== null) {
            $data['szamlaszamElotag'] = $this->prefix;
        }

        if ($this->correctionToPay !== null && $this->correctionToPay !== 0.0) {
            $data['fizetendoKorrekcio'] = $this->correctionToPay;
        }

        if ($this->paid === true) {
            $data['fizetve'] = true;
        }

        if ($this->profitVat === true) {
            $data['arresAfa'] = true;
        }

        $data['eusAfa'] = $this->euVat === true;

        if ($this->invoiceTemplate !== null) {
            $data['szamlaSablon'] = $this->resolveInvoiceTemplate();
        }

        if ($this->previewPdf === true) {
            $data['elonezetpdf'] = true;
        }

        return $data;
    }

    protected function resolveDate(Carbon|string $date): string
    {
        if ($date instanceof Carbon) {
            return $date->format('Y-m-d');
        }

        return $date;
    }

    protected function resolveLanguage(): string
    {
        if ($this->language instanceof Language) {
            return $this->language->value;
        }

        return (string) $this->language;
    }

    protected function resolveInvoiceTemplate(): string
    {
        if ($this->invoiceTemplate instanceof InvoiceTemplate) {
            return $this->invoiceTemplate->value;
        }

        return (string) $this->invoiceTemplate;
    }

    public function setInvoiceNumber(?string $invoiceNumber): static
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    public function setIssueDate(Carbon|string|null $issueDate): static
    {
        $this->issueDate = $issueDate;

        return $this;
    }

    public function setPaymentMethod(PaymentMethod|string|null $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function setCurrency(Currency|string|null $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function setLanguage(Language|string|null $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function setFulfillment(Carbon|string|null $fulfillment): static
    {
        $this->fulfillment = $fulfillment;

        return $this;
    }

    public function setPaymentDue(Carbon|string|null $paymentDue): static
    {
        $this->paymentDue = $paymentDue;

        return $this;
    }

    public function setPrefix(?string $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function setExtraLogo(?string $extraLogo): static
    {
        $this->extraLogo = $extraLogo;

        return $this;
    }

    public function setCorrectionToPay(?float $correctionToPay): static
    {
        $this->correctionToPay = $correctionToPay;

        return $this;
    }

    public function setCorrectivedNumber(?string $correctivedNumber): static
    {
        $this->correctivedNumber = $correctivedNumber;

        return $this;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function setExchangeBank(?string $exchangeBank): static
    {
        $this->exchangeBank = $exchangeBank;

        return $this;
    }

    public function setExchangeRate(?float $exchangeRate): static
    {
        $this->exchangeRate = $exchangeRate;

        return $this;
    }

    public function setOrderNumber(?string $orderNumber): static
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    public function setProformaNumber(?string $proformaNumber): static
    {
        $this->proformaNumber = $proformaNumber;

        return $this;
    }

    public function setPaid(?bool $paid): static
    {
        $this->paid = $paid;

        return $this;
    }

    public function setProfitVat(?bool $profitVat): static
    {
        $this->profitVat = $profitVat;

        return $this;
    }

    public function setInvoiceTemplate(InvoiceTemplate|string|null $invoiceTemplate): static
    {
        $this->invoiceTemplate = $invoiceTemplate;

        return $this;
    }

    public function setPrePaymentInvoiceNumber(?string $prePaymentInvoiceNumber): static
    {
        $this->prePaymentInvoiceNumber = $prePaymentInvoiceNumber;

        return $this;
    }

    public function setPreviewPdf(?bool $previewPdf): static
    {
        $this->previewPdf = $previewPdf;

        return $this;
    }

    public function setEuVat(?bool $euVat): static
    {
        $this->euVat = $euVat;

        return $this;
    }
}
