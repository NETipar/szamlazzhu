<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Document;

use NETipar\Szamlazzhu\Header\DocumentHeader;
use NETipar\Szamlazzhu\Header\ReverseReceiptHeader;

class ReverseReceipt extends Document
{
    public function __construct(
        public ReverseReceiptHeader $header = new ReverseReceiptHeader,
    ) {
    }

    public function getHeader(): DocumentHeader
    {
        return $this->header;
    }

    public function setHeader(ReverseReceiptHeader $header): static
    {
        $this->header = $header;

        return $this;
    }

    /** @param array<string, mixed> $settings */
    public function toXmlArray(array $settings): array
    {
        $data = [];

        $data['beallitasok'] = $settings;
        $data['fejlec'] = $this->header->toReverseXmlArray();

        return $data;
    }
}
