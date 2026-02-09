<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\CreditNote;

abstract class CreditNote
{
    public function __construct(
        public string $paymentMode,
        public float $amount = 0.0,
        public string $description = '',
    ) {
    }

    abstract public function toXmlArray(): array;

    public function setPaymentMode(string $paymentMode): static
    {
        $this->paymentMode = $paymentMode;

        return $this;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
