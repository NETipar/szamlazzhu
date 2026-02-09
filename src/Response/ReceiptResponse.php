<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Response;

use NETipar\Szamlazzhu\Enums\ResponseType;
use NETipar\Szamlazzhu\Response\Contracts\ResponseContract;
use SimpleXMLElement;

class ReceiptResponse implements ResponseContract
{
    protected ?int $id = null;

    protected ?string $receiptNumber = null;

    protected ?string $type = null;

    protected bool $reversed = false;

    protected ?string $reversedReceiptNumber = null;

    protected ?string $created = null;

    protected ?string $paymentMethod = null;

    protected ?string $currency = null;

    protected bool $test = false;

    protected array $items = [];

    protected array $amounts = [];

    protected array $creditNotes = [];

    protected ?string $errorCode = null;

    protected ?string $errorMessage = null;

    protected ?string $pdfData = null;

    protected bool $success = false;

    public static function parseData(array $data, ResponseType $type = ResponseType::Text): self
    {
        $response = new self;

        if ($type === ResponseType::Text && isset($data['body'])) {
            $xmlContent = base64_decode($data['body']);
            $params = new SimpleXMLElement($xmlContent);
            $data = self::toArray($params);
        }

        $base = $data['nyugta']['alap'] ?? [];

        if (isset($base['id'])) {
            $response->id = (int) $base['id'];
        }

        if (isset($base['nyugtaszam'])) {
            $response->receiptNumber = $base['nyugtaszam'];
        }

        if (isset($base['tipus'])) {
            $response->type = $base['tipus'];
        }

        if (isset($base['stornozott'])) {
            $response->reversed = $base['stornozott'] === 'true';
        }

        if (isset($base['stornozottNyugtaszam'])) {
            $response->reversedReceiptNumber = $base['stornozottNyugtaszam'];
        }

        if (isset($base['kelt'])) {
            $response->created = $base['kelt'];
        }

        if (isset($base['fizmod'])) {
            $response->paymentMethod = $base['fizmod'];
        }

        if (isset($base['penznem'])) {
            $response->currency = $base['penznem'];
        }

        if (isset($base['teszt'])) {
            $response->test = $base['teszt'] === 'true';
        }

        if (isset($data['nyugta']['tetelek'])) {
            $response->items = $data['nyugta']['tetelek'];
        }

        if (isset($data['nyugta']['osszegek'])) {
            $response->amounts = $data['nyugta']['osszegek'];
        }

        if (isset($data['nyugta']['kifizetesek'])) {
            $response->creditNotes = $data['nyugta']['kifizetesek'];
        }

        if (isset($data['sikeres'])) {
            $response->success = $data['sikeres'] === 'true';
        }

        if (isset($data['nyugtaPdf'])) {
            $response->pdfData = $data['nyugtaPdf'];
        }

        if (isset($data['hibakod']) && ! is_array($data['hibakod'])) {
            $response->errorCode = (string) $data['hibakod'];
        }

        if (isset($data['hibauzenet']) && ! is_array($data['hibauzenet'])) {
            $response->errorMessage = (string) $data['hibauzenet'];
        }

        return $response;
    }

    public function getDocumentNumber(): ?string
    {
        return $this->receiptNumber;
    }

    public function getReceiptNumber(): ?string
    {
        return $this->receiptNumber;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function isReversed(): bool
    {
        return $this->reversed;
    }

    public function getReversedReceiptNumber(): ?string
    {
        return $this->reversedReceiptNumber;
    }

    public function getCreated(): ?string
    {
        return $this->created;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function isTest(): bool
    {
        return $this->test;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getAmounts(): array
    {
        return $this->amounts;
    }

    public function getCreditNotes(): array
    {
        return $this->creditNotes;
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

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function isError(): bool
    {
        return ! $this->isSuccess();
    }

    /**
     * @return array<string, mixed>
     */
    private static function toArray(SimpleXMLElement $xml): array
    {
        $json = json_encode($xml);

        return json_decode($json, true);
    }
}
