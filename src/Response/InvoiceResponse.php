<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Response;

use NETipar\Szamlazzhu\Enums\ResponseType;
use NETipar\Szamlazzhu\Response\Contracts\ResponseContract;

class InvoiceResponse implements ResponseContract
{
    public const INVOICE_NOTIFICATION_SEND_FAILED = 56;

    protected ?string $invoiceNumber = null;

    protected ?int $invoiceIdentifier = null;

    protected ?string $userAccountUrl = null;

    protected ?float $netPrice = null;

    protected ?float $grossAmount = null;

    protected ?float $receivablesAmount = null;

    protected ?string $errorCode = null;

    protected ?string $errorMessage = null;

    protected ?string $pdfData = null;

    protected bool $success = false;

    protected array $headers = [];

    public static function parseData(array $data, ResponseType $type = ResponseType::Text): self
    {
        $response = new self;
        $headers = array_change_key_case($data['headers'] ?? [], CASE_LOWER);
        $isPdf = self::isPdfResponse($data);
        $pdfFile = '';

        if (isset($data['body'])) {
            $pdfFile = $data['body'];
        } elseif ($type === ResponseType::Xml && isset($data['pdf'])) {
            $pdfFile = $data['pdf'];
        }

        if (empty($headers)) {
            return $response;
        }

        $response->headers = $headers;

        if (array_key_exists('szlahu_szamlaszam', $headers)) {
            $response->invoiceNumber = $headers['szlahu_szamlaszam'];
        }

        if (array_key_exists('szlahu_id', $headers)) {
            $response->invoiceIdentifier = (int) $headers['szlahu_id'];
        }

        if (array_key_exists('szlahu_vevoifiokurl', $headers)) {
            $response->userAccountUrl = rawurldecode($headers['szlahu_vevoifiokurl']);
        }

        if (array_key_exists('szlahu_kintlevoseg', $headers)) {
            $response->receivablesAmount = (float) $headers['szlahu_kintlevoseg'];
        }

        if (array_key_exists('szlahu_nettovegosszeg', $headers)) {
            $response->netPrice = (float) $headers['szlahu_nettovegosszeg'];
        }

        if (array_key_exists('szlahu_bruttovegosszeg', $headers)) {
            $response->grossAmount = (float) $headers['szlahu_bruttovegosszeg'];
        }

        if (array_key_exists('szlahu_error', $headers)) {
            $response->errorMessage = urldecode($headers['szlahu_error']);
        }

        if (array_key_exists('szlahu_error_code', $headers)) {
            $response->errorCode = $headers['szlahu_error_code'];
        }

        if ($isPdf && $pdfFile !== '') {
            $response->pdfData = $pdfFile;
        }

        if (! $response->isError()) {
            $response->success = true;
        }

        return $response;
    }

    protected static function isPdfResponse(array $result): bool
    {
        if (isset($result['pdf'])) {
            return true;
        }

        if (isset($result['headers']['content-type']) && $result['headers']['content-type'] === 'application/pdf') {
            return true;
        }

        if (isset($result['headers']['content-disposition']) && stripos($result['headers']['content-disposition'], 'pdf') !== false) {
            return true;
        }

        return false;
    }

    public function getDocumentNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function hasInvoiceNumber(): bool
    {
        return $this->invoiceNumber !== null && $this->invoiceNumber !== '';
    }

    public function getInvoiceIdentifier(): ?int
    {
        return $this->invoiceIdentifier;
    }

    public function getUserAccountUrl(): ?string
    {
        return $this->userAccountUrl !== null ? urldecode($this->userAccountUrl) : null;
    }

    public function getNetPrice(): ?float
    {
        return $this->netPrice;
    }

    public function getGrossAmount(): ?float
    {
        return $this->grossAmount;
    }

    public function getReceivablesAmount(): ?float
    {
        return $this->receivablesAmount;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getPdfFile(): string
    {
        $pdfData = $this->pdfData ?? '';

        return base64_decode($pdfData);
    }

    public function getPdfData(): ?string
    {
        return $this->pdfData;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function isSuccess(): bool
    {
        return $this->success && ! $this->isError();
    }

    public function isError(): bool
    {
        if (! empty($this->errorMessage) || ! empty($this->errorCode)) {
            if ($this->hasInvoiceNumber() && $this->hasInvoiceNotificationSendError()) {
                return false;
            }

            return true;
        }

        return false;
    }

    public function hasInvoiceNotificationSendError(): bool
    {
        return $this->errorCode !== null && (int) $this->errorCode === self::INVOICE_NOTIFICATION_SEND_FAILED;
    }
}
