<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Document;

use NETipar\Szamlazzhu\CreditNote\InvoiceCreditNote;
use NETipar\Szamlazzhu\Entity\Buyer;
use NETipar\Szamlazzhu\Entity\Seller;
use NETipar\Szamlazzhu\Header\DocumentHeader;
use NETipar\Szamlazzhu\Header\InvoiceHeader;
use NETipar\Szamlazzhu\Waybill\Waybill;

class Invoice extends Document
{
    /** @deprecated Use LookupType::InvoiceNumber instead */
    public const FROM_INVOICE_NUMBER = 1;

    /** @deprecated Use LookupType::OrderNumber instead */
    public const FROM_ORDER_NUMBER = 2;

    /** @deprecated Use LookupType::ExternalId instead */
    public const FROM_INVOICE_EXTERNAL_ID = 3;

    public const CREDIT_NOTES_LIMIT = 5;

    public const ATTACHMENTS_LIMIT = 5;

    /**
     * @param  InvoiceCreditNote[]  $creditNotes
     * @param  string[]  $attachments
     */
    public function __construct(
        public InvoiceHeader $header = new InvoiceHeader,
        public ?Seller $seller = null,
        public ?Buyer $buyer = null,
        public ?Waybill $waybill = null,
        public array $creditNotes = [],
        public bool $additive = true,
        public array $attachments = [],
    ) {
    }

    public function getHeader(): DocumentHeader
    {
        return $this->header;
    }

    public function setHeader(InvoiceHeader $header): static
    {
        $this->header = $header;

        return $this;
    }

    public function setSeller(?Seller $seller): static
    {
        $this->seller = $seller;

        return $this;
    }

    public function setBuyer(?Buyer $buyer): static
    {
        $this->buyer = $buyer;

        return $this;
    }

    public function setWaybill(?Waybill $waybill): static
    {
        $this->waybill = $waybill;

        return $this;
    }

    public function addCreditNote(InvoiceCreditNote $creditNote): static
    {
        if (count($this->creditNotes) < self::CREDIT_NOTES_LIMIT) {
            $this->creditNotes[] = $creditNote;
        }

        return $this;
    }

    /** @param InvoiceCreditNote[] $creditNotes */
    public function setCreditNotes(array $creditNotes): static
    {
        $this->creditNotes = $creditNotes;

        return $this;
    }

    public function setAdditive(bool $additive): static
    {
        $this->additive = $additive;

        return $this;
    }

    public function addAttachment(string $filePath): static
    {
        if (count($this->attachments) >= self::ATTACHMENTS_LIMIT) {
            throw new \InvalidArgumentException(
                'Maximum '.self::ATTACHMENTS_LIMIT.' attachments allowed per invoice.'
            );
        }

        if (! file_exists($filePath)) {
            throw new \InvalidArgumentException("Attachment file does not exist: {$filePath}");
        }

        $this->attachments[] = $filePath;

        return $this;
    }

    /** @param array<string, mixed> $settings */
    public function toXmlArray(array $settings): array
    {
        $data = [];

        $data['beallitasok'] = $settings;
        $data['fejlec'] = $this->header->toXmlArray();

        if ($this->seller !== null) {
            $data['elado'] = $this->seller->toXmlArray();
        }

        if ($this->buyer !== null) {
            $data['vevo'] = $this->buyer->toXmlArray();
        }

        if ($this->waybill !== null) {
            $data['fuvarlevel'] = $this->waybill->toXmlArray();
        }

        if (! empty($this->items)) {
            $data['tetelek'] = $this->buildXmlItemsData();
        }

        return $data;
    }

    public function toCreditsXmlArray(array $settings): array
    {
        $data = [];

        $data['beallitasok'] = $settings;

        if (! empty($this->creditNotes)) {
            foreach ($this->creditNotes as $key => $note) {
                $data["note{$key}"] = $note->toXmlArray();
            }
        }

        return $data;
    }

    public function toReverseXmlArray(array $settings): array
    {
        $data = [];

        $data['beallitasok'] = $settings;
        $data['fejlec'] = $this->header->toXmlArray();

        if ($this->seller !== null) {
            $data['elado'] = $this->seller->toXmlArray();
        }

        if ($this->buyer !== null) {
            $data['vevo'] = $this->buyer->toXmlArray();
        }

        return $data;
    }
}
