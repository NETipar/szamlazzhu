<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Response;

use NETipar\Szamlazzhu\Response\Contracts\ResponseContract;

class TaxPayerResponse implements ResponseContract
{
    protected ?string $requestId = null;

    protected ?string $timestamp = null;

    protected ?string $requestVersion = null;

    protected ?string $funcCode = null;

    protected bool $taxpayerValidity = false;

    protected array $taxpayerData = [];

    protected ?string $errorCode = null;

    protected ?string $errorMessage = null;

    public static function parseData(array $data): self
    {
        $response = new self;

        if (isset($data['result']['funcCode'])) {
            $response->funcCode = $data['result']['funcCode'];
        }

        if (isset($data['result']['errorCode'])) {
            $response->errorCode = (string) $data['result']['errorCode'];
        }

        if (isset($data['result']['message'])) {
            $response->errorMessage = $data['result']['message'];
        }

        if (isset($data['taxpayerValidity'])) {
            $response->taxpayerValidity = $data['taxpayerValidity'] === 'true';
        }

        if (isset($data['header'])) {
            $response->requestId = $data['header']['requestId'] ?? null;
            $response->timestamp = $data['header']['timestamp'] ?? null;
            $response->requestVersion = $data['header']['requestVersion'] ?? null;
        }

        if (isset($data['taxpayerData'])) {
            $response->taxpayerData = $data['taxpayerData'];
        }

        return $response;
    }

    public function getDocumentNumber(): ?string
    {
        return null;
    }

    public function getRequestId(): ?string
    {
        return $this->requestId;
    }

    public function getTimestamp(): ?string
    {
        return $this->timestamp;
    }

    public function getRequestVersion(): ?string
    {
        return $this->requestVersion;
    }

    public function getFuncCode(): ?string
    {
        return $this->funcCode;
    }

    public function isTaxpayerValid(): bool
    {
        return $this->taxpayerValidity;
    }

    public function hasTaxPayerData(): bool
    {
        return ! empty($this->taxpayerData);
    }

    public function getTaxPayerData(): array
    {
        return $this->taxpayerData;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function isSuccess(): bool
    {
        return $this->funcCode === 'OK';
    }

    public function isError(): bool
    {
        return ! $this->isSuccess();
    }
}
