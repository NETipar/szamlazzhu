<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Document;

use NETipar\Szamlazzhu\Header\DocumentHeader;
use NETipar\Szamlazzhu\Header\ReverseInvoiceHeader;

class ReverseInvoice extends Document
{
    public function __construct(
        public ReverseInvoiceHeader $header = new ReverseInvoiceHeader,
    ) {
    }

    public function getHeader(): DocumentHeader
    {
        return $this->header;
    }

    public function setHeader(ReverseInvoiceHeader $header): static
    {
        $this->header = $header;

        return $this;
    }

    /** @param array<string, mixed> $settings */
    public function toXmlArray(array $settings): array
    {
        $data = [];

        $data['beallitasok'] = $settings;
        $data['fejlec'] = $this->header->toXmlArray();

        return $data;
    }
}
