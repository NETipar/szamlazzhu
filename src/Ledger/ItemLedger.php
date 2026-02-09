<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Ledger;

abstract class ItemLedger
{
    public function __construct(
        public string $revenueLedgerNumber = '',
        public string $vatLedgerNumber = '',
    ) {
    }

    abstract public function toXmlArray(): array;

    public function setRevenueLedgerNumber(string $revenueLedgerNumber): static
    {
        $this->revenueLedgerNumber = $revenueLedgerNumber;

        return $this;
    }

    public function setVatLedgerNumber(string $vatLedgerNumber): static
    {
        $this->vatLedgerNumber = $vatLedgerNumber;

        return $this;
    }
}
