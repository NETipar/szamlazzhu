<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Waybill;

use NETipar\Szamlazzhu\Enums\WaybillType;

class MplWaybill extends Waybill
{
    public string $buyerCode = '';

    public string $weight = '';

    public string $service = '';

    public ?float $insuredValue = null;

    public function __construct(string $destination = '', string $barcode = '', string $comment = '')
    {
        parent::__construct($destination, WaybillType::Mpl->value, $barcode, $comment);
    }

    public function toXmlArray(): array
    {
        $data = parent::toXmlArray();

        $mpl = [];
        $mpl['vevokod'] = $this->buyerCode;
        $mpl['vonalkod'] = $this->barcode;
        $mpl['tomeg'] = $this->weight;

        if ($this->service !== '') {
            $mpl['kulonszolgaltatasok'] = $this->service;
        }

        if ($this->insuredValue !== null) {
            $mpl['erteknyilvanitas'] = number_format($this->insuredValue, 2, '.', '');
        }

        $data['mpl'] = $mpl;

        return $data;
    }

    public function setBuyerCode(string $buyerCode): static
    {
        $this->buyerCode = $buyerCode;

        return $this;
    }

    public function setWeight(string $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function setService(string $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function setInsuredValue(float $insuredValue): static
    {
        $this->insuredValue = $insuredValue;

        return $this;
    }
}
