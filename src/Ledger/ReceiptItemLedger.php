<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Ledger;

class ReceiptItemLedger extends ItemLedger
{
    public function toXmlArray(): array
    {
        $data = [];

        if ($this->revenueLedgerNumber !== '') {
            $data['arbevetel'] = $this->revenueLedgerNumber;
        }

        if ($this->vatLedgerNumber !== '') {
            $data['afa'] = $this->vatLedgerNumber;
        }

        return $data;
    }
}
