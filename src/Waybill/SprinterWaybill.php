<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Waybill;

use NETipar\Szamlazzhu\Enums\WaybillType;

class SprinterWaybill extends Waybill
{
    public string $id = '';

    public string $senderId = '';

    public string $shipmentZip = '';

    public ?int $packetNumber = null;

    public string $barcodePostfix = '';

    public string $shippingTime = '';

    public function __construct(string $destination = '', string $barcode = '', string $comment = '')
    {
        parent::__construct($destination, WaybillType::Sprinter->value, $barcode, $comment);
    }

    public function toXmlArray(): array
    {
        $data = parent::toXmlArray();

        $sprinter = [];

        if ($this->id !== '') {
            $sprinter['azonosito'] = $this->id;
        }

        if ($this->senderId !== '') {
            $sprinter['feladokod'] = $this->senderId;
        }

        if ($this->shipmentZip !== '') {
            $sprinter['iranykod'] = $this->shipmentZip;
        }

        if ($this->packetNumber !== null) {
            $sprinter['csomagszam'] = $this->packetNumber;
        }

        if ($this->barcodePostfix !== '') {
            $sprinter['vonalkodPostfix'] = $this->barcodePostfix;
        }

        if ($this->shippingTime !== '') {
            $sprinter['szallitasiIdo'] = $this->shippingTime;
        }

        $data['sprinter'] = $sprinter;

        return $data;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function setSenderId(string $senderId): static
    {
        $this->senderId = $senderId;

        return $this;
    }

    public function setShipmentZip(string $shipmentZip): static
    {
        $this->shipmentZip = $shipmentZip;

        return $this;
    }

    public function setPacketNumber(int $packetNumber): static
    {
        $this->packetNumber = $packetNumber;

        return $this;
    }

    public function setBarcodePostfix(string $barcodePostfix): static
    {
        $this->barcodePostfix = $barcodePostfix;

        return $this;
    }

    public function setShippingTime(string $shippingTime): static
    {
        $this->shippingTime = $shippingTime;

        return $this;
    }
}
