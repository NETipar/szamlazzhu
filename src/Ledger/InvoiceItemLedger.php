<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Ledger;

class InvoiceItemLedger extends ItemLedger
{
    public string $settlementPeriodStart = '';

    public string $settlementPeriodEnd = '';

    public function __construct(
        public string $economicEventType = '',
        public string $vatEconomicEventType = '',
        string $revenueLedgerNumber = '',
        string $vatLedgerNumber = '',
    ) {
        parent::__construct($revenueLedgerNumber, $vatLedgerNumber);
    }

    public function toXmlArray(): array
    {
        $data = [];

        if ($this->economicEventType !== '') {
            $data['gazdasagiEsem'] = $this->economicEventType;
        }

        if ($this->vatEconomicEventType !== '') {
            $data['gazdasagiEsemAfa'] = $this->vatEconomicEventType;
        }

        if ($this->revenueLedgerNumber !== '') {
            $data['arbevetelFokonyviSzam'] = $this->revenueLedgerNumber;
        }

        if ($this->vatLedgerNumber !== '') {
            $data['afaFokonyviSzam'] = $this->vatLedgerNumber;
        }

        if ($this->settlementPeriodStart !== '') {
            $data['elszDatumTol'] = $this->settlementPeriodStart;
        }

        if ($this->settlementPeriodEnd !== '') {
            $data['elszDatumIg'] = $this->settlementPeriodEnd;
        }

        return $data;
    }

    public function setEconomicEventType(string $economicEventType): static
    {
        $this->economicEventType = $economicEventType;

        return $this;
    }

    public function setVatEconomicEventType(string $vatEconomicEventType): static
    {
        $this->vatEconomicEventType = $vatEconomicEventType;

        return $this;
    }

    public function setSettlementPeriodStart(string $settlementPeriodStart): static
    {
        $this->settlementPeriodStart = $settlementPeriodStart;

        return $this;
    }

    public function setSettlementPeriodEnd(string $settlementPeriodEnd): static
    {
        $this->settlementPeriodEnd = $settlementPeriodEnd;

        return $this;
    }
}
