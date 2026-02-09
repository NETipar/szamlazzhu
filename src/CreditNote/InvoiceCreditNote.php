<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\CreditNote;

class InvoiceCreditNote extends CreditNote
{
    public function __construct(
        public string $date,
        float $amount,
        string $paymentMode = 'átutalás',
        string $description = '',
    ) {
        parent::__construct($paymentMode, $amount, $description);
    }

    public function toXmlArray(): array
    {
        $data = [];

        if ($this->date !== '') {
            $data['datum'] = $this->date;
        }

        if ($this->paymentMode !== '') {
            $data['jogcim'] = $this->paymentMode;
        }

        if ($this->amount !== 0.0) {
            $data['osszeg'] = number_format($this->amount, 2, '.', '');
        }

        if ($this->description !== '') {
            $data['leiras'] = $this->description;
        }

        return $data;
    }

    public function setDate(string $date): static
    {
        $this->date = $date;

        return $this;
    }
}
