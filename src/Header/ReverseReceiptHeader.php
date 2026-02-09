<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Header;

use NETipar\Szamlazzhu\Enums\DocumentType;

class ReverseReceiptHeader extends ReceiptHeader
{
    public function documentType(): DocumentType
    {
        return DocumentType::ReverseReceipt;
    }
}
