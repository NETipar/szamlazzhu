<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Entity;

class Buyer
{
    public function __construct(
        public ?string $name = null,
        public ?string $country = null,
        public ?string $zipCode = null,
        public ?string $city = null,
        public ?string $address = null,
        public ?string $email = null,
        public ?bool $sendEmail = null,
        public ?string $taxNumber = null,
        public ?string $groupId = null,
        public ?string $taxNumberEu = null,
        public ?string $postalName = null,
        public ?string $postalCountry = null,
        public ?string $postalZipCode = null,
        public ?string $postalCity = null,
        public ?string $postalAddress = null,
        public ?string $signatoryName = null,
        public ?string $phoneNumber = null,
        public ?string $comment = null,
        public ?BuyerLedger $ledgerData = null,
        public ?int $taxPayer = null,
        public ?string $id = null,
    ) {
    }

    public function toXmlArray(): array
    {
        $data = [
            'nev' => $this->name,
            'orszag' => $this->country,
            'irsz' => $this->zipCode,
            'telepules' => $this->city,
            'cim' => $this->address,
        ];

        if ($this->email !== null) {
            $data['email'] = $this->email;
        }

        if ($this->sendEmail !== null) {
            $data['sendEmail'] = $this->sendEmail;
        }

        if ($this->taxPayer !== null) {
            $data['adoalany'] = $this->taxPayer;
        }

        if ($this->taxNumber !== null) {
            $data['adoszam'] = $this->taxNumber;
        }

        if ($this->groupId !== null) {
            $data['csoportazonosito'] = $this->groupId;
        }

        if ($this->taxNumberEu !== null) {
            $data['adoszamEU'] = $this->taxNumberEu;
        }

        if ($this->postalName !== null) {
            $data['postazasiNev'] = $this->postalName;
        }

        if ($this->postalCountry !== null) {
            $data['postazasiOrszag'] = $this->postalCountry;
        }

        if ($this->postalZipCode !== null) {
            $data['postazasiIrsz'] = $this->postalZipCode;
        }

        if ($this->postalCity !== null) {
            $data['postazasiTelepules'] = $this->postalCity;
        }

        if ($this->postalAddress !== null) {
            $data['postazasiCim'] = $this->postalAddress;
        }

        if ($this->ledgerData !== null) {
            $data['vevoFokonyv'] = $this->ledgerData->toXmlArray();
        }

        if ($this->id !== null) {
            $data['azonosito'] = $this->id;
        }

        if ($this->signatoryName !== null) {
            $data['alairoNeve'] = $this->signatoryName;
        }

        if ($this->phoneNumber !== null) {
            $data['telefonszam'] = $this->phoneNumber;
        }

        if ($this->comment !== null) {
            $data['megjegyzes'] = $this->comment;
        }

        return $data;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function setZipCode(?string $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function setSendEmail(?bool $sendEmail): static
    {
        $this->sendEmail = $sendEmail;

        return $this;
    }

    public function setTaxNumber(?string $taxNumber): static
    {
        $this->taxNumber = $taxNumber;

        return $this;
    }

    public function setGroupId(?string $groupId): static
    {
        $this->groupId = $groupId;

        return $this;
    }

    public function setTaxNumberEu(?string $taxNumberEu): static
    {
        $this->taxNumberEu = $taxNumberEu;

        return $this;
    }

    public function setPostalName(?string $postalName): static
    {
        $this->postalName = $postalName;

        return $this;
    }

    public function setPostalCountry(?string $postalCountry): static
    {
        $this->postalCountry = $postalCountry;

        return $this;
    }

    public function setPostalZipCode(?string $postalZipCode): static
    {
        $this->postalZipCode = $postalZipCode;

        return $this;
    }

    public function setPostalCity(?string $postalCity): static
    {
        $this->postalCity = $postalCity;

        return $this;
    }

    public function setPostalAddress(?string $postalAddress): static
    {
        $this->postalAddress = $postalAddress;

        return $this;
    }

    public function setSignatoryName(?string $signatoryName): static
    {
        $this->signatoryName = $signatoryName;

        return $this;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function setLedgerData(?BuyerLedger $ledgerData): static
    {
        $this->ledgerData = $ledgerData;

        return $this;
    }

    public function setTaxPayer(?int $taxPayer): static
    {
        $this->taxPayer = $taxPayer;

        return $this;
    }

    public function setId(?string $id): static
    {
        $this->id = $id;

        return $this;
    }
}
