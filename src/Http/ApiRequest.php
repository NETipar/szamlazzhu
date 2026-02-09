<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Http;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use NETipar\Szamlazzhu\Exceptions\ConnectionException;
use NETipar\Szamlazzhu\Exceptions\SzamlazzhuException;
use NETipar\Szamlazzhu\Exceptions\XmlBuildException;
use NETipar\Szamlazzhu\Session\SessionManager;
use NETipar\Szamlazzhu\Support\HasLogging;
use NETipar\Szamlazzhu\Support\XmlBuilder;
use NETipar\Szamlazzhu\Support\XmlValidator;

class ApiRequest
{
    use HasLogging;

    private string $apiUrl;

    private string $apiKey;

    private int $timeout;

    private int $connectTimeout;

    private ?string $certificationPath;

    private ?string $logChannelConfig;

    public function __construct(
        array $config,
        private readonly XmlBuilder $xmlBuilder,
        private readonly XmlValidator $xmlValidator,
        private readonly SessionManager $sessionManager,
    ) {
        $this->apiUrl = $config['api_url'] ?? 'https://www.szamlazz.hu/szamla/';
        $this->apiKey = $config['api_key'] ?? '';
        $this->timeout = (int) ($config['timeout'] ?? 30);
        $this->connectTimeout = (int) ($config['connect_timeout'] ?? 0);
        $this->certificationPath = $config['certification_path'] ?? null;
        $this->logChannelConfig = $config['log_channel'] ?? null;
    }

    /**
     * @param  array<string, string>  $attachments
     * @return array{headers: array<string, string>, body: string}
     */
    public function send(string $type, array $xmlData, array $attachments = []): array
    {
        $schemaMapping = XmlBuilder::getSchemaMapping();

        if (! isset($schemaMapping[$type])) {
            throw new SzamlazzhuException(SzamlazzhuException::REQUEST_TYPE_NOT_EXISTS.": {$type}");
        }

        $mapping = $schemaMapping[$type];
        $xmlName = $mapping['schema'];
        $fileName = $mapping['fileName'];
        $xsdDir = $mapping['xsdDir'];

        $xmlNs = XmlBuilder::getXmlNs($xmlName);
        $schemaLocation = XmlBuilder::getSchemaLocation($xmlName, $xsdDir);
        $xmlContent = $this->xmlBuilder->buildXml($xmlName, $xmlData, $xmlNs, $schemaLocation);

        $this->validateXml($xmlContent);
        $this->log('XML request built successfully.');

        $response = $this->sendRequest($fileName, $xmlContent, $attachments);
        $this->log('API request completed.');

        $headers = $this->parseResponseHeaders($response);
        $headers['schema-type'] = $this->getSchemaType($xmlName);

        $this->handleSessionCookie($response);

        return [
            'headers' => $headers,
            'body' => $response->body(),
        ];
    }

    protected function logChannel(): ?string
    {
        return $this->logChannelConfig;
    }

    private function validateXml(string $xmlContent): void
    {
        $errors = $this->xmlValidator->checkValidXml($xmlContent);

        if (! empty($errors)) {
            $error = $errors[0];

            throw new XmlBuildException(
                SzamlazzhuException::XML_NOT_VALID." a {$error->line}. sorban: {$error->message}."
            );
        }
    }

    /**
     * @param  array<string, string>  $attachments
     */
    private function sendRequest(string $fileName, string $xmlContent, array $attachments = []): Response
    {
        $apiKeyHash = md5($this->apiKey);
        $sessionId = $this->sessionManager->getSessionId($apiKeyHash);

        $pendingRequest = Http::timeout($this->timeout)
            ->withHeaders($this->buildHeaders());

        if ($this->connectTimeout > 0) {
            $pendingRequest = $pendingRequest->connectTimeout($this->connectTimeout);
        }

        if ($this->certificationPath !== null && file_exists($this->certificationPath)) {
            $pendingRequest = $pendingRequest->withOptions([
                'verify' => $this->certificationPath,
            ]);
        }

        if ($sessionId !== null) {
            $pendingRequest = $pendingRequest->withCookies(
                ['JSESSIONID' => $sessionId],
                parse_url($this->apiUrl, PHP_URL_HOST) ?: 'www.szamlazz.hu'
            );
        }

        $pendingRequest = $pendingRequest->attach(
            $fileName,
            $xmlContent,
            $fileName.'.xml'
        );

        foreach ($attachments as $index => $attachmentPath) {
            if (! file_exists($attachmentPath)) {
                $this->log("Attachment not found: {$attachmentPath}", 'warning');

                continue;
            }

            $attachName = 'attachfile'.($index + 1);
            $pendingRequest = $pendingRequest->attach(
                $attachName,
                file_get_contents($attachmentPath),
                basename($attachmentPath)
            );
        }

        try {
            $response = $pendingRequest->post($this->apiUrl);
        } catch (\Exception $e) {
            throw new ConnectionException(
                SzamlazzhuException::CONNECTION_ERROR.' - '.$e->getMessage(),
                0,
                $e
            );
        }

        return $response;
    }

    /**
     * @return array<int, string>
     */
    private function buildHeaders(): array
    {
        return [
            'charset' => 'UTF-8',
            'PHP' => PHP_VERSION,
            'API' => '2.0',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function parseResponseHeaders(Response $response): array
    {
        $headers = [];

        foreach ($response->headers() as $key => $values) {
            $headers[strtolower($key)] = is_array($values) ? implode(', ', $values) : $values;
        }

        $headers['http_code'] = 'HTTP/1.1 '.$response->status();

        return $headers;
    }

    private function handleSessionCookie(Response $response): void
    {
        $cookies = $response->cookies();
        $sessionId = null;

        foreach ($cookies as $cookie) {
            if ($cookie->getName() === 'JSESSIONID') {
                $sessionId = $cookie->getValue();

                break;
            }
        }

        if ($sessionId !== null) {
            $apiKeyHash = md5($this->apiKey);
            $this->sessionManager->setSessionId($apiKeyHash, $sessionId);
            $this->log('Session ID updated.');
        }
    }

    private function getSchemaType(string $xmlName): string
    {
        return match ($xmlName) {
            XmlBuilder::XML_SCHEMA_CREATE_INVOICE,
            XmlBuilder::XML_SCHEMA_CREATE_REVERSE_INVOICE,
            XmlBuilder::XML_SCHEMA_PAY_INVOICE,
            XmlBuilder::XML_SCHEMA_REQUEST_INVOICE_XML,
            XmlBuilder::XML_SCHEMA_REQUEST_INVOICE_PDF => 'invoice',
            XmlBuilder::XML_SCHEMA_DELETE_PROFORMA => 'proforma',
            XmlBuilder::XML_SCHEMA_CREATE_RECEIPT,
            XmlBuilder::XML_SCHEMA_CREATE_REVERSE_RECEIPT,
            XmlBuilder::XML_SCHEMA_SEND_RECEIPT,
            XmlBuilder::XML_SCHEMA_GET_RECEIPT => 'receipt',
            XmlBuilder::XML_SCHEMA_TAXPAYER => 'taxpayer',
            default => 'invoice',
        };
    }
}
