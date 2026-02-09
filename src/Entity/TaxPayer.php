<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Entity;

class TaxPayer
{
    public function __construct(
        public string $taxPayerId,
    ) {
        $this->taxPayerId = substr($taxPayerId, 0, 8);
    }

    public function toXmlArray(): array
    {
        return [
            'torzsszam' => $this->taxPayerId,
        ];
    }

    public function setTaxPayerId(string $taxPayerId): static
    {
        $this->taxPayerId = substr($taxPayerId, 0, 8);

        return $this;
    }
}
