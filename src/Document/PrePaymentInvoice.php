<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Document;

use NETipar\Szamlazzhu\Header\PrePaymentInvoiceHeader;

class PrePaymentInvoice extends Invoice
{
    public function __construct()
    {
        parent::__construct(header: new PrePaymentInvoiceHeader);
    }
}
