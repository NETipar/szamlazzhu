<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Http;

use Illuminate\Support\Facades\Storage;
use NETipar\Szamlazzhu\Enums\ResponseType;
use NETipar\Szamlazzhu\Enums\SchemaType;
use NETipar\Szamlazzhu\Exceptions\ResponseException;
use NETipar\Szamlazzhu\Exceptions\SzamlazzhuException;
use NETipar\Szamlazzhu\Response\Contracts\ResponseContract;
use NETipar\Szamlazzhu\Response\InvoiceResponse;
use NETipar\Szamlazzhu\Response\ProformaDeletionResponse;
use NETipar\Szamlazzhu\Response\ReceiptResponse;
use NETipar\Szamlazzhu\Response\TaxPayerResponse;
use NETipar\Szamlazzhu\Support\HasLogging;
use NETipar\Szamlazzhu\Support\SimpleXmlExtended;
use SimpleXMLElement;

class ApiResponse
{
    use HasLogging;

    private ?string $documentNumber = null;

    private ?string $pdfFile = null;

    private ?string $errorMsg = null;

    private ?int $errorCode = null;

    private bool $success = false;

    private ?SimpleXMLElement $xmlData = null;

    private ?string $content = null;

    private ?ResponseContract $responseObj = null;

    private SchemaType $schemaType;

    private ResponseType $responseType;

    private array $config;

    /**
     * @param  array{headers: array<string, string>, body: string}  $rawResponse
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        private readonly array $rawResponse,
        ResponseType $responseType,
        array $config = [],
    ) {
        $this->schemaType = SchemaType::tryFrom($rawResponse['headers']['schema-type'] ?? 'invoice') ?? SchemaType::Invoice;
        $this->responseType = $responseType;
        $this->config = $config;
    }

    public function handleResponse(): self
    {
        if (empty($this->rawResponse)) {
            throw new ResponseException(SzamlazzhuException::AGENT_RESPONSE_IS_EMPTY);
        }

        $headers = $this->rawResponse['headers'] ?? [];

        if (empty($headers)) {
            throw new ResponseException(SzamlazzhuException::AGENT_RESPONSE_NO_HEADER);
        }

        $headers = array_change_key_case($headers, CASE_LOWER);

        if (isset($headers['szlahu_down']) && $headers['szlahu_down'] !== '') {
            throw new ResponseException(SzamlazzhuException::SYSTEM_DOWN, 500);
        }

        if (! isset($this->rawResponse['body']) || $this->rawResponse['body'] === '') {
            throw new ResponseException(SzamlazzhuException::AGENT_RESPONSE_NO_CONTENT);
        }

        if ($this->isXmlResponse()) {
            $this->buildResponseXmlData();
        } else {
            $this->buildResponseTextData();
        }

        $this->buildResponseObjData();

        if ($this->config['save_response_xml'] ?? false) {
            $this->saveResponseXml();
        }

        $this->checkFields();

        if ($this->hasInvoiceNotificationSendError()) {
            $this->log(SzamlazzhuException::INVOICE_NOTIFICATION_SEND_FAILED);
        }

        if ($this->isFailed()) {
            throw new ResponseException(
                SzamlazzhuException::AGENT_ERROR.": [{$this->errorCode}], {$this->errorMsg}"
            );
        }

        if ($this->isSuccess() && ! $this->isTaxPayerResponse()) {
            $this->processSuccessResponse();
        }

        return $this;
    }

    private function processSuccessResponse(): void
    {
        $responseObj = $this->responseObj;

        if ($responseObj === null) {
            return;
        }

        $this->documentNumber = $responseObj->getDocumentNumber();
        $downloadPdf = $this->config['download_pdf'] ?? true;

        if (! $downloadPdf) {
            $this->content = $this->rawResponse['body'];

            return;
        }

        if (! method_exists($responseObj, 'getPdfFile')) {
            return;
        }

        $pdfData = $responseObj->getPdfFile();

        if (empty($pdfData)) {
            return;
        }

        $this->pdfFile = $pdfData;

        if ($this->config['save_pdf'] ?? false) {
            $this->savePdf();
        }
    }

    private function savePdf(): void
    {
        if ($this->pdfFile === null || $this->documentNumber === null) {
            return;
        }

        $disk = $this->config['storage']['disk'] ?? 'local';
        $pdfPath = $this->config['storage']['pdf_path'] ?? 'szamlazzhu/pdf';
        $fileName = $pdfPath.'/'.$this->documentNumber.'.pdf';

        Storage::disk($disk)->put($fileName, $this->pdfFile);
        $this->log(SzamlazzhuException::PDF_FILE_SAVE_SUCCESS.": {$fileName}");
    }

    private function saveResponseXml(): void
    {
        if ($this->xmlData === null) {
            return;
        }

        $disk = $this->config['storage']['disk'] ?? 'local';
        $xmlPath = $this->config['storage']['xml_path'] ?? 'szamlazzhu/xml';

        $name = $this->schemaType->value;
        $postfix = $this->isFailed() ? 'error-' : '';

        $typeSuffix = match ($this->responseType) {
            ResponseType::Xml, ResponseType::TaxPayerXml => '-xml',
            ResponseType::Text => '-text',
        };

        $fileName = $xmlPath.'/response-'.$postfix.$name.$typeSuffix.'-'.date('Y-m-d_H-i-s').'.xml';

        if ($this->isTaxPayerResponse()) {
            Storage::disk($disk)->put($fileName, $this->rawResponse['body']);
        } else {
            Storage::disk($disk)->put($fileName, $this->xmlData->asXML());
        }
    }

    private function checkFields(): void
    {
        if ($this->isInvoiceResponse() && $this->responseType === ResponseType::Text) {
            $keys = implode(',', array_keys($this->rawResponse['headers']));

            if (! preg_match('/(szlahu_)/', $keys)) {
                throw new ResponseException(SzamlazzhuException::NO_SZLAHU_KEY_IN_HEADER);
            }
        }
    }

    private function buildResponseTextData(): void
    {
        $xmlData = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><response></response>');
        $headers = $xmlData->addChild('headers');

        foreach ($this->rawResponse['headers'] as $key => $value) {
            $headers->addChild($key, htmlspecialchars((string) $value));
        }

        if ($this->isReceiptResponse()) {
            $content = base64_encode($this->rawResponse['body']);
        } else {
            $downloadPdf = $this->config['download_pdf'] ?? true;
            $content = $downloadPdf ? base64_encode($this->rawResponse['body']) : $this->rawResponse['body'];
        }

        $xmlData->addChild('body', $content);
        $this->xmlData = $xmlData;
    }

    private function buildResponseXmlData(): void
    {
        if ($this->isTaxPayerResponse()) {
            $xmlData = new SimpleXmlExtended($this->rawResponse['body']);
            $xmlData = $this->removeNamespaces($xmlData);
            $this->xmlData = $xmlData;
        } else {
            $xmlData = new SimpleXMLElement($this->rawResponse['body']);
            $headers = $xmlData->addChild('headers');

            foreach ($this->rawResponse['headers'] as $key => $value) {
                $headers->addChild($key, htmlspecialchars((string) $value));
            }

            $this->xmlData = $xmlData;
        }
    }

    private function buildResponseObjData(): void
    {
        $data = $this->getDataArray();
        $result = $data['result'] ?? [];

        if ($this->isInvoiceResponse()) {
            $this->responseObj = InvoiceResponse::parseData($result, $this->responseType);
        } elseif ($this->isProformaResponse()) {
            $this->responseObj = ProformaDeletionResponse::parseData($result);
        } elseif ($this->isReceiptResponse()) {
            $this->responseObj = ReceiptResponse::parseData($result, $this->responseType);
        } elseif ($this->isTaxPayerResponse()) {
            $this->responseObj = TaxPayerResponse::parseData($result);
        }

        if ($this->responseObj === null) {
            return;
        }

        if ($this->responseObj->isError() || $this->hasInvoiceNotificationSendError()) {
            $this->errorCode = $this->responseObj->getErrorCode() !== null
                ? (int) $this->responseObj->getErrorCode()
                : null;
            $this->errorMsg = $this->responseObj->getErrorMessage();
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function getDataArray(): array
    {
        $result = [];

        if (! $this->isTaxPayerResponse()) {
            $result['documentNumber'] = $this->documentNumber;
        }

        if ($this->xmlData !== null) {
            $jsonEncoded = json_encode($this->xmlData);
            $result['result'] = json_decode($jsonEncoded, true);
        } else {
            $result['result'] = $this->content;
        }

        return $result;
    }

    private function removeNamespaces(SimpleXMLElement $xml): SimpleXMLElement
    {
        $xmlString = $xml->asXML();
        $cleanedXml = preg_replace('/xmlns[^=]*="[^"]*"/i', '', $xmlString);
        $cleanedXml = preg_replace('/<(\/?)([a-zA-Z0-9]+):/', '<$1', $cleanedXml);
        $cleanedXml = preg_replace('/\s{2,}/', ' ', $cleanedXml);

        return new SimpleXmlExtended($cleanedXml);
    }

    public function toPdf(): ?string
    {
        return $this->pdfFile;
    }

    public function toXml(): ?string
    {
        if ($this->xmlData === null) {
            return null;
        }

        return $this->xmlData->asXML() ?: null;
    }

    public function toJson(): ?string
    {
        $data = $this->getDataArray();
        $json = json_encode($data);

        if ($json === false) {
            return null;
        }

        return $json;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function toArray(): ?array
    {
        return $this->getDataArray();
    }

    public function isSuccess(): bool
    {
        return ! $this->isFailed();
    }

    public function isFailed(): bool
    {
        if ($this->responseObj === null) {
            return true;
        }

        return $this->responseObj->isError();
    }

    public function getDocumentNumber(): ?string
    {
        return $this->documentNumber;
    }

    public function getResponseObj(): ?ResponseContract
    {
        return $this->responseObj;
    }

    public function getErrorMsg(): ?string
    {
        return $this->errorMsg;
    }

    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    public function getPdfFile(): ?string
    {
        return $this->pdfFile;
    }

    public function hasInvoiceNotificationSendError(): bool
    {
        if (! $this->isInvoiceResponse() || $this->responseObj === null) {
            return false;
        }

        if ($this->responseObj instanceof InvoiceResponse) {
            return $this->responseObj->hasInvoiceNotificationSendError();
        }

        return false;
    }

    public function getTaxPayerData(): ?string
    {
        if (! $this->isTaxPayerResponse()) {
            return null;
        }

        return $this->rawResponse['body'];
    }

    private function isInvoiceResponse(): bool
    {
        return $this->schemaType === SchemaType::Invoice;
    }

    private function isProformaResponse(): bool
    {
        return $this->schemaType === SchemaType::Proforma;
    }

    private function isReceiptResponse(): bool
    {
        return $this->schemaType === SchemaType::Receipt;
    }

    private function isTaxPayerResponse(): bool
    {
        return $this->schemaType === SchemaType::TaxPayer;
    }

    private function isXmlResponse(): bool
    {
        if ($this->isTaxPayerResponse() && $this->responseType === ResponseType::TaxPayerXml) {
            return true;
        }

        if ($this->isInvoiceResponse() && $this->responseType === ResponseType::Xml) {
            return true;
        }

        if ($this->isReceiptResponse() && $this->responseType === ResponseType::Xml) {
            return true;
        }

        return false;
    }

    protected function logChannel(): ?string
    {
        return $this->config['log_channel'] ?? null;
    }
}
