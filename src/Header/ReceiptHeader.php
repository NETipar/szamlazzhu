<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Header;

use NETipar\Szamlazzhu\Enums\Currency;
use NETipar\Szamlazzhu\Enums\DocumentType;
use NETipar\Szamlazzhu\Enums\PaymentMethod;

class ReceiptHeader extends DocumentHeader
{
    public ?string $receiptNumber = null;

    public ?string $callId = null;

    public ?string $prefix = null;

    public PaymentMethod|string|null $paymentMethod = null;

    public Currency|string|null $currency = null;

    public ?string $exchangeBank = null;

    public ?float $exchangeRate = null;

    public ?string $comment = null;

    public ?string $pdfTemplate = null;

    public ?string $buyerLedgerId = null;

    public function documentType(): DocumentType
    {
        return DocumentType::Receipt;
    }

    public function toXmlArray(): array
    {
        $data = [];

        if ($this->callId !== null) {
            $data['hivasAzonosito'] = $this->callId;
        }

        if ($this->prefix !== null) {
            $data['elotag'] = $this->prefix;
        }

        if ($this->paymentMethod !== null) {
            $data['fizmod'] = $this->resolvePaymentMethod($this->paymentMethod);
        }

        if ($this->currency !== null) {
            $data['penznem'] = $this->resolveCurrency($this->currency);
        }

        if ($this->exchangeBank !== null) {
            $data['devizabank'] = $this->exchangeBank;
        }

        if ($this->exchangeRate !== null) {
            $data['devizaarf'] = $this->exchangeRate;
        }

        if ($this->comment !== null) {
            $data['megjegyzes'] = $this->comment;
        }

        if ($this->pdfTemplate !== null) {
            $data['pdfSablon'] = $this->pdfTemplate;
        }

        if ($this->buyerLedgerId !== null) {
            $data['fokonyvVevo'] = $this->buyerLedgerId;
        }

        return $data;
    }

    public function toReverseXmlArray(): array
    {
        $data = [];

        if ($this->receiptNumber !== null) {
            $data['nyugtaszam'] = $this->receiptNumber;
        }

        if ($this->pdfTemplate !== null) {
            $data['pdfSablon'] = $this->pdfTemplate;
        }

        if ($this->callId !== null) {
            $data['hivasAzonosito'] = $this->callId;
        }

        return $data;
    }

    public function toGetXmlArray(): array
    {
        $data = [];

        if ($this->receiptNumber !== null) {
            $data['nyugtaszam'] = $this->receiptNumber;
        }

        if ($this->pdfTemplate !== null) {
            $data['pdfSablon'] = $this->pdfTemplate;
        }

        return $data;
    }

    public function toSendXmlArray(): array
    {
        $data = [];

        if ($this->receiptNumber !== null) {
            $data['nyugtaszam'] = $this->receiptNumber;
        }

        return $data;
    }

    public function setReceiptNumber(?string $receiptNumber): static
    {
        $this->receiptNumber = $receiptNumber;

        return $this;
    }

    public function setCallId(?string $callId): static
    {
        $this->callId = $callId;

        return $this;
    }

    public function setPrefix(?string $prefix): static
    {
        $this->prefix = $prefix;

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

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function setPdfTemplate(?string $pdfTemplate): static
    {
        $this->pdfTemplate = $pdfTemplate;

        return $this;
    }

    public function setBuyerLedgerId(?string $buyerLedgerId): static
    {
        $this->buyerLedgerId = $buyerLedgerId;

        return $this;
    }
}
