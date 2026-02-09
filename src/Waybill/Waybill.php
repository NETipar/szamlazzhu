<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Waybill;

abstract class Waybill
{
    public function __construct(
        public string $destination = '',
        public string $parcel = '',
        public string $barcode = '',
        public string $comment = '',
    ) {
    }

    public function toXmlArray(): array
    {
        $data = [];

        if ($this->destination !== '') {
            $data['uticel'] = $this->destination;
        }

        if ($this->parcel !== '') {
            $data['futarSzolgalat'] = $this->parcel;
        }

        if ($this->barcode !== '') {
            $data['vonalkod'] = $this->barcode;
        }

        if ($this->comment !== '') {
            $data['megjegyzes'] = $this->comment;
        }

        return $data;
    }

    public function setDestination(string $destination): static
    {
        $this->destination = $destination;

        return $this;
    }

    public function setParcel(string $parcel): static
    {
        $this->parcel = $parcel;

        return $this;
    }

    public function setBarcode(string $barcode): static
    {
        $this->barcode = $barcode;

        return $this;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }
}
