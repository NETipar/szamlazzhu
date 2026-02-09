<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Document;

use NETipar\Szamlazzhu\Header\ProformaHeader;

class Proforma extends Invoice
{
    /** @deprecated Use LookupType::InvoiceNumber instead */
    public const FROM_INVOICE_NUMBER = 1;

    /** @deprecated Use LookupType::OrderNumber instead */
    public const FROM_ORDER_NUMBER = 2;

    public function __construct()
    {
        parent::__construct(header: new ProformaHeader);
    }

    public function toDeleteXmlArray(array $settings): array
    {
        $data = [];

        $data['beallitasok'] = $settings;

        /** @var ProformaHeader $header */
        $header = $this->header;
        $data['fejlec'] = $header->toDeleteXmlArray();

        return $data;
    }
}
