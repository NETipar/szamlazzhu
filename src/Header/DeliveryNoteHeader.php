<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Header;

use NETipar\Szamlazzhu\Enums\DocumentType;

class DeliveryNoteHeader extends InvoiceHeader
{
    public function __construct()
    {
        $this->setPaid(false);
    }

    public function documentType(): DocumentType
    {
        return DocumentType::DeliveryNote;
    }
}
