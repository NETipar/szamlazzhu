<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Item;

abstract class Item
{
    public ?string $name = null;

    public ?string $id = null;

    public ?float $quantity = null;

    public ?string $quantityUnit = null;

    public ?float $netUnitPrice = null;

    public string|float|null $vat = null;

    public ?float $priceGapVatBase = null;

    public ?float $netPrice = null;

    public ?float $vatAmount = null;

    public ?float $grossAmount = null;

    public ?string $comment = null;

    public ?int $dataDeletionCode = null;

    abstract public function toXmlArray(): array;

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function setId(?string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function setQuantity(?float $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function setQuantityUnit(?string $quantityUnit): static
    {
        $this->quantityUnit = $quantityUnit;

        return $this;
    }

    public function setNetUnitPrice(?float $netUnitPrice): static
    {
        $this->netUnitPrice = $netUnitPrice;

        return $this;
    }

    public function setVat(string|float|null $vat): static
    {
        $this->vat = $vat;

        return $this;
    }

    public function setPriceGapVatBase(?float $priceGapVatBase): static
    {
        $this->priceGapVatBase = $priceGapVatBase;

        return $this;
    }

    public function setNetPrice(?float $netPrice): static
    {
        $this->netPrice = $netPrice;

        return $this;
    }

    public function setVatAmount(?float $vatAmount): static
    {
        $this->vatAmount = $vatAmount;

        return $this;
    }

    public function setGrossAmount(?float $grossAmount): static
    {
        $this->grossAmount = $grossAmount;

        return $this;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function setDataDeletionCode(?int $dataDeletionCode): static
    {
        $this->dataDeletionCode = $dataDeletionCode;

        return $this;
    }
}
