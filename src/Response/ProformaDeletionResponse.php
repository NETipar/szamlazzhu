<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Response;

use NETipar\Szamlazzhu\Response\Contracts\ResponseContract;

class ProformaDeletionResponse implements ResponseContract
{
    protected ?string $errorCode = null;

    protected ?string $errorMessage = null;

    protected bool $success = false;

    protected array $headers = [];

    public static function parseData(array $data): self
    {
        $response = new self;
        $headers = array_change_key_case($data['headers'] ?? [], CASE_LOWER);

        if (empty($headers)) {
            return $response;
        }

        $response->headers = $headers;

        if (array_key_exists('szlahu_error', $headers)) {
            $response->errorMessage = urldecode($headers['szlahu_error']);
        }

        if (array_key_exists('szlahu_error_code', $headers)) {
            $response->errorCode = $headers['szlahu_error_code'];
        }

        if (! $response->isError()) {
            $response->success = true;
        }

        return $response;
    }

    public function getDocumentNumber(): ?string
    {
        return null;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
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
        return ! empty($this->errorMessage) || ! empty($this->errorCode);
    }
}
