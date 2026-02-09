<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Header;

use NETipar\Szamlazzhu\Enums\DocumentType;

class ReverseInvoiceHeader extends InvoiceHeader
{
    public function documentType(): DocumentType
    {
        return DocumentType::ReverseInvoice;
    }

    public function toXmlArray(): array
    {
        $data = [];

        $data['szamlaszam'] = $this->invoiceNumber;

        if ($this->issueDate !== null) {
            $data['keltDatum'] = $this->resolveDate($this->issueDate);
        }

        if ($this->fulfillment !== null) {
            $data['teljesitesDatum'] = $this->resolveDate($this->fulfillment);
        }

        if ($this->comment !== null) {
            $data['megjegyzes'] = $this->comment;
        }

        $data['tipus'] = 'SS';

        if ($this->invoiceTemplate !== null) {
            $data['szamlaSablon'] = $this->resolveInvoiceTemplate();
        }

        return $data;
    }
}
