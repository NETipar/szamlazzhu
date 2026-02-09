<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Response\Contracts;

interface ResponseContract
{
    public function getDocumentNumber(): ?string;

    public function getErrorCode(): ?string;

    public function getErrorMessage(): ?string;

    public function isSuccess(): bool;

    public function isError(): bool;
}
