<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu;

use NETipar\Szamlazzhu\Document\CorrectiveInvoice;
use NETipar\Szamlazzhu\Document\DeliveryNote;
use NETipar\Szamlazzhu\Document\Document;
use NETipar\Szamlazzhu\Document\FinalInvoice;
use NETipar\Szamlazzhu\Document\Invoice;
use NETipar\Szamlazzhu\Document\PrePaymentInvoice;
use NETipar\Szamlazzhu\Document\Proforma;
use NETipar\Szamlazzhu\Document\Receipt;
use NETipar\Szamlazzhu\Document\ReverseInvoice;
use NETipar\Szamlazzhu\Document\ReverseReceipt;
use NETipar\Szamlazzhu\Entity\TaxPayer;
use NETipar\Szamlazzhu\Enums\LookupType;
use NETipar\Szamlazzhu\Enums\RequestType;
use NETipar\Szamlazzhu\Enums\ResponseType;
use NETipar\Szamlazzhu\Exceptions\SzamlazzhuException;
use NETipar\Szamlazzhu\Exceptions\ValidationException;
use NETipar\Szamlazzhu\Http\ApiRequestFactory;
use NETipar\Szamlazzhu\Http\ApiResponse;
use NETipar\Szamlazzhu\Session\SessionManager;
use NETipar\Szamlazzhu\Support\HasLogging;

class Client
{
    use HasLogging;

    /** @var array<string, mixed> */
    private array $config;

    private SessionManager $sessionManager;

    private ApiRequestFactory $apiRequestFactory;

    /** @var array<string, string> */
    private array $customHeaders = [];

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        array $config,
        SessionManager $sessionManager,
        ?ApiRequestFactory $apiRequestFactory = null,
    ) {
        $this->config = $config;
        $this->sessionManager = $sessionManager;
        $this->apiRequestFactory = $apiRequestFactory ?? new ApiRequestFactory;
    }

    public function generateInvoice(Invoice $invoice): ApiResponse
    {
        return $this->generateDocument(RequestType::GenerateInvoice, $invoice);
    }

    public function generatePrePaymentInvoice(PrePaymentInvoice $invoice): ApiResponse
    {
        return $this->generateDocument(RequestType::GeneratePrePaymentInvoice, $invoice);
    }

    public function generateFinalInvoice(FinalInvoice $invoice): ApiResponse
    {
        return $this->generateDocument(RequestType::GenerateFinalInvoice, $invoice);
    }

    public function generateCorrectiveInvoice(CorrectiveInvoice $invoice): ApiResponse
    {
        return $this->generateDocument(RequestType::GenerateCorrectiveInvoice, $invoice);
    }

    public function generateReverseInvoice(ReverseInvoice $invoice): ApiResponse
    {
        return $this->generateDocument(RequestType::GenerateReverseInvoice, $invoice);
    }

    public function generateReceipt(Receipt $receipt): ApiResponse
    {
        return $this->generateDocument(RequestType::GenerateReceipt, $receipt);
    }

    public function generateReverseReceipt(ReverseReceipt $receipt): ApiResponse
    {
        return $this->generateDocument(RequestType::GenerateReverseReceipt, $receipt);
    }

    public function generateProforma(Proforma $proforma): ApiResponse
    {
        return $this->generateDocument(RequestType::GenerateProforma, $proforma);
    }

    public function generateDeliveryNote(DeliveryNote $deliveryNote): ApiResponse
    {
        return $this->generateDocument(RequestType::GenerateDeliveryNote, $deliveryNote);
    }

    public function payInvoice(Invoice $invoice): ApiResponse
    {
        $this->log('Számla jóváírás rögzítése.');

        $configOverride = [
            'response_type' => ResponseType::Text->value,
        ];

        return $this->sendRequest(RequestType::PayInvoice, $invoice, $configOverride);
    }

    public function sendReceipt(Receipt $receipt): ApiResponse
    {
        return $this->sendRequest(RequestType::SendReceipt, $receipt);
    }

    public function getInvoiceData(
        string $data,
        LookupType $type = LookupType::InvoiceNumber,
        bool $downloadPdf = false,
    ): ApiResponse {
        $invoice = $this->buildInvoiceLookup($data, $type);

        $configOverride = [
            'download_pdf' => $downloadPdf,
            'response_type' => ResponseType::Xml->value,
        ];

        return $this->sendRequest(RequestType::RequestInvoiceData, $invoice, $configOverride);
    }

    public function getInvoicePdf(string $data, LookupType $type = LookupType::InvoiceNumber): ApiResponse
    {
        $invoice = $this->buildInvoiceLookup($data, $type);

        $configOverride = [
            'download_pdf' => true,
        ];

        return $this->sendRequest(RequestType::RequestInvoicePdf, $invoice, $configOverride);
    }

    public function getReceiptData(string $receiptNumber, LookupType $type = LookupType::DocumentNumber): ApiResponse
    {
        $receipt = $this->buildReceiptLookup($receiptNumber, $type);

        return $this->sendRequest(RequestType::RequestReceiptData, $receipt);
    }

    public function getReceiptPdf(string $receiptNumber, LookupType $type = LookupType::DocumentNumber): ApiResponse
    {
        $receipt = $this->buildReceiptLookup($receiptNumber, $type);

        return $this->sendRequest(RequestType::RequestReceiptPdf, $receipt);
    }

    public function getTaxPayer(string $taxPayerId): ApiResponse
    {
        $taxPayer = new TaxPayer($taxPayerId);

        $configOverride = [
            'response_type' => ResponseType::TaxPayerXml->value,
        ];

        return $this->sendRequest(RequestType::GetTaxPayer, $taxPayer, $configOverride);
    }

    public function deleteProforma(string $data, LookupType $type = LookupType::InvoiceNumber): ApiResponse
    {
        $proforma = new Proforma;

        if ($type === LookupType::InvoiceNumber) {
            $proforma->header->setInvoiceNumber($data);
        } else {
            $proforma->header->setOrderNumber($data);
        }

        $configOverride = [
            'response_type' => ResponseType::Xml->value,
            'download_pdf' => false,
        ];

        return $this->sendRequest(RequestType::DeleteProforma, $proforma, $configOverride);
    }

    public function getApiKey(): string
    {
        return $this->config['api_key'] ?? '';
    }

    public function getApiUrl(): string
    {
        return $this->config['api_url'] ?? 'https://www.szamlazz.hu/szamla/';
    }

    public function isDownloadPdf(): bool
    {
        return (bool) ($this->config['download_pdf'] ?? true);
    }

    public function getResponseType(): ResponseType
    {
        $value = $this->config['response_type'] ?? ResponseType::Text->value;

        return ResponseType::from((int) $value);
    }

    public function getTimeout(): int
    {
        return (int) ($this->config['timeout'] ?? 30);
    }

    public function getConnectTimeout(): int
    {
        return (int) ($this->config['connect_timeout'] ?? 0);
    }

    public function getAggregator(): string
    {
        return $this->config['aggregator'] ?? '';
    }

    public function addCustomHeader(string $key, string $value): static
    {
        $this->customHeaders[$key] = $value;

        return $this;
    }

    public function removeCustomHeader(string $key): static
    {
        unset($this->customHeaders[$key]);

        return $this;
    }

    /** @return array<string, string> */
    public function getCustomHeaders(): array
    {
        return $this->customHeaders;
    }

    protected function logChannel(): ?string
    {
        return $this->config['log_channel'] ?? null;
    }

    private function generateDocument(RequestType $type, Document $document): ApiResponse
    {
        $this->log("Bizonylat generálás: {$type->value}");

        return $this->sendRequest($type, $document);
    }

    /**
     * @param  array<string, mixed>  $configOverride
     */
    private function sendRequest(RequestType $type, Document|TaxPayer $entity, array $configOverride = []): ApiResponse
    {
        $mergedConfig = array_merge($this->config, $configOverride);
        $settings = $this->buildSettings($type, $entity, $mergedConfig);
        $xmlData = $this->buildXmlData($type, $entity, $settings);
        $attachments = $this->getAttachments($entity);

        $apiRequest = $this->apiRequestFactory->make($mergedConfig, $this->sessionManager, $this->customHeaders);

        $rawResponse = $apiRequest->send($type->value, $xmlData, $attachments);

        $responseType = $this->resolveResponseType($type, $mergedConfig);
        $response = new ApiResponse($rawResponse, $responseType, $mergedConfig);

        return $response->handleResponse();
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array<string, mixed>
     */
    private function buildSettings(RequestType $requestType, Document|TaxPayer $entity, array $config): array
    {
        $settings = [
            'szamlaagentkulcs' => $config['api_key'] ?? '',
        ];

        return $settings + match (true) {
            $requestType->isInvoiceCreation() => $this->invoiceCreationSettings($entity, $config),
            $requestType === RequestType::GenerateReverseInvoice => $this->reverseInvoiceSettings($entity, $config),
            $requestType === RequestType::PayInvoice => $this->payInvoiceSettings($entity, $config),
            $requestType === RequestType::RequestInvoiceData => $this->invoiceLookupSettings($entity, $config, forPdf: false),
            $requestType === RequestType::RequestInvoicePdf => $this->invoiceLookupSettings($entity, $config, forPdf: true),
            $requestType->isReceiptType() => ['pdfLetoltes' => (bool) ($config['download_pdf'] ?? true)],
            default => [],
        };
    }

    /**
     * The agent XSD requires valaszVerzio before aggregator for invoice creation.
     *
     * @param  array<string, mixed>  $config
     * @return array<string, mixed>
     */
    private function invoiceCreationSettings(Document|TaxPayer $entity, array $config): array
    {
        return [
            'eszamla' => $entity instanceof Document && $entity->getHeader()->isInvoice(),
            'szamlaLetoltes' => (bool) ($config['download_pdf'] ?? true),
            'szamlaLetoltesPld' => 1,
            'valaszVerzio' => (int) ($config['response_type'] ?? ResponseType::Text->value),
            'aggregator' => $config['aggregator'] ?? '',
        ];
    }

    /**
     * The agentst XSD requires aggregator before valaszVerzio for reverse invoices.
     *
     * @param  array<string, mixed>  $config
     * @return array<string, mixed>
     */
    private function reverseInvoiceSettings(Document|TaxPayer $entity, array $config): array
    {
        return [
            'eszamla' => $entity instanceof Document && $entity->getHeader()->isInvoice(),
            'szamlaLetoltes' => (bool) ($config['download_pdf'] ?? true),
            'szamlaLetoltesPld' => 1,
            'aggregator' => $config['aggregator'] ?? '',
            'valaszVerzio' => (int) ($config['response_type'] ?? ResponseType::Text->value),
        ];
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array<string, mixed>
     */
    private function payInvoiceSettings(Document|TaxPayer $entity, array $config): array
    {
        if (! $entity instanceof Invoice) {
            return [];
        }

        return [
            'szamlaszam' => $entity->header->invoiceNumber ?? '',
            'additiv' => $entity->additive,
            'aggregator' => $config['aggregator'] ?? '',
            'valaszVerzio' => ResponseType::Text->value,
        ];
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array<string, mixed>
     */
    private function invoiceLookupSettings(Document|TaxPayer $entity, array $config, bool $forPdf): array
    {
        if (! $entity instanceof Invoice) {
            return [];
        }

        $settings = [
            'szamlaszam' => $entity->header->invoiceNumber ?? '',
            'rendelesSzam' => $entity->header->orderNumber ?? '',
        ];

        if ($forPdf) {
            $settings['valaszVerzio'] = (int) ($config['response_type'] ?? ResponseType::Text->value);
        }

        if (! $forPdf) {
            $settings['pdf'] = (bool) ($config['download_pdf'] ?? true);
        }

        if ($entity->header->externalId !== null) {
            $settings['szamlaKulsoAzon'] = $entity->header->externalId;
        }

        return $settings;
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    private function buildXmlData(RequestType $type, Document|TaxPayer $entity, array $settings): array
    {
        if ($entity instanceof TaxPayer) {
            return array_merge(
                ['beallitasok' => $settings],
                $entity->toXmlArray(),
            );
        }

        return match ($type) {
            RequestType::PayInvoice => $entity instanceof Invoice
                ? $entity->toCreditsXmlArray($settings)
                : $entity->toXmlArray($settings),
            RequestType::SendReceipt => $entity instanceof Receipt
                ? $entity->toSendXmlArray($settings)
                : $entity->toXmlArray($settings),
            RequestType::RequestInvoiceData, RequestType::RequestInvoicePdf => $settings,
            RequestType::RequestReceiptData, RequestType::RequestReceiptPdf => $entity instanceof Receipt
                ? $entity->toGetXmlArray($settings)
                : $entity->toXmlArray($settings),
            RequestType::DeleteProforma => $entity instanceof Proforma
                ? $entity->toDeleteXmlArray($settings)
                : $entity->toXmlArray($settings),
            default => $entity->toXmlArray($settings),
        };
    }

    /** @return array<int, string> */
    private function getAttachments(Document|TaxPayer $entity): array
    {
        if ($entity instanceof Invoice) {
            return $entity->attachments;
        }

        return [];
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function resolveResponseType(RequestType $type, array $config): ResponseType
    {
        if ($type === RequestType::GetTaxPayer) {
            return ResponseType::TaxPayerXml;
        }

        if ($type === RequestType::RequestInvoiceData || $type === RequestType::DeleteProforma) {
            return ResponseType::Xml;
        }

        $value = $config['response_type'] ?? ResponseType::Text->value;

        return ResponseType::from((int) $value);
    }

    private function buildInvoiceLookup(string $data, LookupType $type): Invoice
    {
        $invoice = new Invoice;

        match ($type) {
            LookupType::InvoiceNumber => $invoice->header->setInvoiceNumber($data),
            LookupType::OrderNumber => $invoice->header->setOrderNumber($data),
            LookupType::ExternalId => $invoice->header->setExternalId($this->validatedExternalId($data)),
        };

        return $invoice;
    }

    private function validatedExternalId(string $externalId): string
    {
        if (trim($externalId) === '') {
            throw new ValidationException(SzamlazzhuException::INVOICE_EXTERNAL_ID_IS_EMPTY);
        }

        return $externalId;
    }

    private function buildReceiptLookup(string $data, LookupType $type): Receipt
    {
        if ($type === LookupType::ExternalId) {
            throw new ValidationException('Nyugta lekérdezés külső azonosító alapján nem támogatott.');
        }

        $receipt = new Receipt;

        if ($type === LookupType::OrderNumber) {
            $receipt->header->setOrderNumber($data);

            return $receipt;
        }

        $receipt->header->setReceiptNumber($data);

        return $receipt;
    }
}
