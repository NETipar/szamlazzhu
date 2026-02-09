<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Document;

use NETipar\Szamlazzhu\CreditNote\ReceiptCreditNote;
use NETipar\Szamlazzhu\Entity\Buyer;
use NETipar\Szamlazzhu\Entity\Seller;
use NETipar\Szamlazzhu\Header\DocumentHeader;
use NETipar\Szamlazzhu\Header\ReceiptHeader;

class Receipt extends Document
{
    public const CREDIT_NOTES_LIMIT = 5;

    /** @param ReceiptCreditNote[] $creditNotes */
    public function __construct(
        public ReceiptHeader $header = new ReceiptHeader,
        public array $creditNotes = [],
        public ?Seller $seller = null,
        public ?Buyer $buyer = null,
    ) {
    }

    public function getHeader(): DocumentHeader
    {
        return $this->header;
    }

    public function setHeader(ReceiptHeader $header): static
    {
        $this->header = $header;

        return $this;
    }

    public function addCreditNote(ReceiptCreditNote $creditNote): static
    {
        if (count($this->creditNotes) < self::CREDIT_NOTES_LIMIT) {
            $this->creditNotes[] = $creditNote;
        }

        return $this;
    }

    /** @param ReceiptCreditNote[] $creditNotes */
    public function setCreditNotes(array $creditNotes): static
    {
        $this->creditNotes = $creditNotes;

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

    /** @param array<string, mixed> $settings */
    public function toXmlArray(array $settings): array
    {
        $data = [];

        $data['beallitasok'] = $settings;
        $data['fejlec'] = $this->header->toXmlArray();

        if (! empty($this->items)) {
            $data['tetelek'] = $this->buildXmlItemsData();
        }

        if (! empty($this->creditNotes)) {
            $data['kifizetesek'] = $this->buildCreditsXmlData();
        }

        return $data;
    }

    public function toReverseXmlArray(array $settings): array
    {
        $data = [];

        $data['beallitasok'] = $settings;
        $data['fejlec'] = $this->header->toReverseXmlArray();

        return $data;
    }

    public function toGetXmlArray(array $settings): array
    {
        $data = [];

        $data['beallitasok'] = $settings;
        $data['fejlec'] = $this->header->toGetXmlArray();

        return $data;
    }

    public function toSendXmlArray(array $settings): array
    {
        $data = [];

        $data['beallitasok'] = $settings;
        $data['fejlec'] = $this->header->toSendXmlArray();

        if ($this->buyer !== null || $this->seller !== null) {
            $emailData = $this->buildXmlEmailSendingData();

            if (! empty($emailData)) {
                $data['emailKuldes'] = $emailData;
            }
        }

        return $data;
    }

    protected function buildCreditsXmlData(): array
    {
        $data = [];

        foreach ($this->creditNotes as $key => $note) {
            $data["note{$key}"] = $note->toXmlArray();
        }

        return $data;
    }

    protected function buildXmlEmailSendingData(): array
    {
        $data = [];

        if ($this->buyer !== null && $this->buyer->email !== null && $this->buyer->email !== '') {
            $data['email'] = $this->buyer->email;
        }

        if ($this->seller !== null) {
            if ($this->seller->emailReplyTo !== null && $this->seller->emailReplyTo !== '') {
                $data['emailReplyto'] = $this->seller->emailReplyTo;
            }

            if ($this->seller->emailSubject !== null && $this->seller->emailSubject !== '') {
                $data['emailTargy'] = $this->seller->emailSubject;
            }

            if ($this->seller->emailContent !== null && $this->seller->emailContent !== '') {
                $data['emailSzoveg'] = $this->seller->emailContent;
            }
        }

        return $data;
    }
}
