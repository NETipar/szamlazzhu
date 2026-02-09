<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Header;

use NETipar\Szamlazzhu\Enums\DocumentType;

class ProformaHeader extends InvoiceHeader
{
    public function __construct()
    {
        $this->setPaid(false);
    }

    public function documentType(): DocumentType
    {
        return DocumentType::Proforma;
    }

    public function toXmlArray(): array
    {
        return parent::toXmlArray();
    }

    public function toDeleteXmlArray(): array
    {
        $data = [];

        if ($this->invoiceNumber !== null) {
            $data['szamlaszam'] = $this->invoiceNumber;
        }

        if ($this->orderNumber !== null) {
            $data['rendelesszam'] = $this->orderNumber;
        }

        return $data;
    }
}
