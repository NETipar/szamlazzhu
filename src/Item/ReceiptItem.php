<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Item;

use NETipar\Szamlazzhu\Ledger\ReceiptItemLedger;

class ReceiptItem extends Item
{
    public ?ReceiptItemLedger $ledgerData = null;

    public function toXmlArray(): array
    {
        $data = [
            'megnevezes' => $this->name,
        ];

        if ($this->id !== null) {
            $data['azonosito'] = $this->id;
        }

        $data['mennyiseg'] = $this->quantity;
        $data['mennyisegiEgyseg'] = $this->quantityUnit;
        $data['nettoEgysegar'] = $this->netUnitPrice;
        $data['afakulcs'] = $this->vat;
        $data['netto'] = $this->netPrice;
        $data['afa'] = $this->vatAmount;
        $data['brutto'] = $this->grossAmount;

        if ($this->comment !== null) {
            $data['megjegyzes'] = $this->comment;
        }

        if ($this->ledgerData !== null) {
            $data['fokonyv'] = $this->ledgerData->toXmlArray();
        }

        if ($this->dataDeletionCode !== null) {
            $data['torloKod'] = $this->dataDeletionCode;
        }

        return $data;
    }

    public function setLedgerData(?ReceiptItemLedger $ledgerData): static
    {
        $this->ledgerData = $ledgerData;

        return $this;
    }
}
