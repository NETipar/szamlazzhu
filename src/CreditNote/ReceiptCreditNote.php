<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\CreditNote;

class ReceiptCreditNote extends CreditNote
{
    public function __construct(
        string $paymentMode = 'készpénz',
        float $amount = 0.0,
        string $description = '',
    ) {
        parent::__construct($paymentMode, $amount, $description);
    }

    public function toXmlArray(): array
    {
        $data = [];

        if ($this->paymentMode !== '') {
            $data['fizetoeszkoz'] = $this->paymentMode;
        }

        if ($this->amount !== 0.0) {
            $data['osszeg'] = number_format($this->amount, 2, '.', '');
        }

        if ($this->description !== '') {
            $data['leiras'] = $this->description;
        }

        return $data;
    }
}
