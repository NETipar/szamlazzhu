<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Header;

use NETipar\Szamlazzhu\Enums\DocumentType;

class FinalInvoiceHeader extends InvoiceHeader
{
    public function documentType(): DocumentType
    {
        return DocumentType::FinalInvoice;
    }
}
