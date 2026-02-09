<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Entity;

class Seller
{
    public function __construct(
        public ?string $bankName = null,
        public ?string $bankAccountNumber = null,
        public ?string $emailReplyTo = null,
        public ?string $emailSubject = null,
        public ?string $emailContent = null,
        public ?string $signatoryName = null,
    ) {
    }

    public function toXmlArray(): array
    {
        $data = [];

        if ($this->bankName !== null) {
            $data['bank'] = $this->bankName;
        }

        if ($this->bankAccountNumber !== null) {
            $data['bankszamlaszam'] = $this->bankAccountNumber;
        }

        if ($this->emailReplyTo !== null) {
            $data['emailReplyto'] = $this->emailReplyTo;
        }

        if ($this->emailSubject !== null) {
            $data['emailTargy'] = $this->emailSubject;
        }

        if ($this->emailContent !== null) {
            $data['emailSzoveg'] = $this->emailContent;
        }

        if ($this->signatoryName !== null) {
            $data['alairoNeve'] = $this->signatoryName;
        }

        return $data;
    }

    public function setBankName(?string $bankName): static
    {
        $this->bankName = $bankName;

        return $this;
    }

    public function setBankAccountNumber(?string $bankAccountNumber): static
    {
        $this->bankAccountNumber = $bankAccountNumber;

        return $this;
    }

    public function setEmailReplyTo(?string $emailReplyTo): static
    {
        $this->emailReplyTo = $emailReplyTo;

        return $this;
    }

    public function setEmailSubject(?string $emailSubject): static
    {
        $this->emailSubject = $emailSubject;

        return $this;
    }

    public function setEmailContent(?string $emailContent): static
    {
        $this->emailContent = $emailContent;

        return $this;
    }

    public function setSignatoryName(?string $signatoryName): static
    {
        $this->signatoryName = $signatoryName;

        return $this;
    }
}
