<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Waybill;

use NETipar\Szamlazzhu\Enums\WaybillType;

class TransoflexWaybill extends Waybill
{
    public string $id = '';

    public string $shippingId = '';

    public ?int $packetNumber = null;

    public string $countryCode = '';

    public string $zip = '';

    public string $service = '';

    public function __construct(string $destination = '', string $barcode = '', string $comment = '')
    {
        parent::__construct($destination, WaybillType::Transoflex->value, $barcode, $comment);
    }

    public function toXmlArray(): array
    {
        $data = parent::toXmlArray();

        $tof = [];

        if ($this->id !== '') {
            $tof['azonosito'] = $this->id;
        }

        if ($this->shippingId !== '') {
            $tof['shippingID'] = $this->shippingId;
        }

        if ($this->packetNumber !== null) {
            $tof['csomagszam'] = $this->packetNumber;
        }

        if ($this->countryCode !== '') {
            $tof['countryCode'] = $this->countryCode;
        }

        if ($this->zip !== '') {
            $tof['zip'] = $this->zip;
        }

        if ($this->service !== '') {
            $tof['service'] = $this->service;
        }

        $data['tof'] = $tof;

        return $data;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function setShippingId(string $shippingId): static
    {
        $this->shippingId = $shippingId;

        return $this;
    }

    public function setPacketNumber(int $packetNumber): static
    {
        $this->packetNumber = $packetNumber;

        return $this;
    }

    public function setCountryCode(string $countryCode): static
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function setZip(string $zip): static
    {
        $this->zip = $zip;

        return $this;
    }

    public function setService(string $service): static
    {
        $this->service = $service;

        return $this;
    }
}
