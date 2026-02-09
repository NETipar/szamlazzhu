<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Item;

use NETipar\Szamlazzhu\Ledger\InvoiceItemLedger;

class InvoiceItem extends Item
{
    public ?InvoiceItemLedger $ledgerData = null;

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

        if ($this->priceGapVatBase !== null) {
            $data['arresAfaAlap'] = $this->priceGapVatBase;
        }

        $data['nettoErtek'] = $this->netPrice;
        $data['afaErtek'] = $this->vatAmount;
        $data['bruttoErtek'] = $this->grossAmount;

        if ($this->comment !== null) {
            $data['megjegyzes'] = $this->comment;
        }

        if ($this->ledgerData !== null) {
            $data['tetelFokonyv'] = $this->ledgerData->toXmlArray();
        }

        if ($this->dataDeletionCode !== null) {
            $data['torloKod'] = $this->dataDeletionCode;
        }

        return $data;
    }

    public function setLedgerData(?InvoiceItemLedger $ledgerData): static
    {
        $this->ledgerData = $ledgerData;

        return $this;
    }
}
