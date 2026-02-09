<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Entity;

class BuyerLedger
{
    public function __construct(
        public ?string $bookingDate = null,
        public ?string $buyerId = null,
        public ?string $buyerLedgerNumber = null,
        public ?bool $continuedFulfillment = null,
        public ?string $settlementPeriodStart = null,
        public ?string $settlementPeriodEnd = null,
    ) {
    }

    public function toXmlArray(): array
    {
        $data = [];

        if ($this->bookingDate !== null) {
            $data['konyvelesDatum'] = $this->bookingDate;
        }

        if ($this->buyerId !== null) {
            $data['vevoAzonosito'] = $this->buyerId;
        }

        if ($this->buyerLedgerNumber !== null) {
            $data['vevoFokonyviSzam'] = $this->buyerLedgerNumber;
        }

        if ($this->continuedFulfillment !== null) {
            $data['folyamatosTelj'] = $this->continuedFulfillment;
        }

        if ($this->settlementPeriodStart !== null) {
            $data['elszDatumTol'] = $this->settlementPeriodStart;
        }

        if ($this->settlementPeriodEnd !== null) {
            $data['elszDatumIg'] = $this->settlementPeriodEnd;
        }

        return $data;
    }

    public function setBookingDate(?string $bookingDate): static
    {
        $this->bookingDate = $bookingDate;

        return $this;
    }

    public function setBuyerId(?string $buyerId): static
    {
        $this->buyerId = $buyerId;

        return $this;
    }

    public function setBuyerLedgerNumber(?string $buyerLedgerNumber): static
    {
        $this->buyerLedgerNumber = $buyerLedgerNumber;

        return $this;
    }

    public function setContinuedFulfillment(?bool $continuedFulfillment): static
    {
        $this->continuedFulfillment = $continuedFulfillment;

        return $this;
    }

    public function setSettlementPeriodStart(?string $settlementPeriodStart): static
    {
        $this->settlementPeriodStart = $settlementPeriodStart;

        return $this;
    }

    public function setSettlementPeriodEnd(?string $settlementPeriodEnd): static
    {
        $this->settlementPeriodEnd = $settlementPeriodEnd;

        return $this;
    }
}
