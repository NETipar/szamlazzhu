<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Waybill;

use NETipar\Szamlazzhu\Enums\WaybillType;

class PppWaybill extends Waybill
{
    public string $barcodePrefix = '';

    public string $barcodePostfix = '';

    public function __construct(string $destination = '', string $barcode = '', string $comment = '')
    {
        parent::__construct($destination, WaybillType::Ppp->value, $barcode, $comment);
    }

    public function toXmlArray(): array
    {
        $data = parent::toXmlArray();

        $ppp = [];

        if ($this->barcodePrefix !== '') {
            $ppp['vonalkodPrefix'] = $this->barcodePrefix;
        }

        if ($this->barcodePostfix !== '') {
            $ppp['vonalkodPostfix'] = $this->barcodePostfix;
        }

        $data['ppp'] = $ppp;

        return $data;
    }

    public function setBarcodePrefix(string $barcodePrefix): static
    {
        $this->barcodePrefix = $barcodePrefix;

        return $this;
    }

    public function setBarcodePostfix(string $barcodePostfix): static
    {
        $this->barcodePostfix = $barcodePostfix;

        return $this;
    }
}
