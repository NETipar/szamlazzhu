<?php

use NETipar\Szamlazzhu\Enums\ResponseType;
use NETipar\Szamlazzhu\Exceptions\ResponseException;
use NETipar\Szamlazzhu\Http\ApiResponse;
use NETipar\Szamlazzhu\Response\InvoiceResponse;

it('parses invoice response from text headers', function () {
    $data = [
        'headers' => [
            'szlahu_szamlaszam' => 'TST-2025-001',
            'szlahu_id' => '12345',
            'szlahu_nettovegosszeg' => '10000',
            'szlahu_bruttovegosszeg' => '12700',
            'szlahu_kintlevoseg' => '12700',
        ],
        'body' => base64_encode('pdf-content'),
    ];

    $response = InvoiceResponse::parseData($data);

    expect($response->getInvoiceNumber())->toBe('TST-2025-001')
        ->and($response->getDocumentNumber())->toBe('TST-2025-001')
        ->and($response->getInvoiceIdentifier())->toBe(12345)
        ->and($response->getNetPrice())->toBe(10000.0)
        ->and($response->getGrossAmount())->toBe(12700.0)
        ->and($response->getReceivablesAmount())->toBe(12700.0)
        ->and($response->isSuccess())->toBeTrue()
        ->and($response->isError())->toBeFalse();
});

it('parses error response', function () {
    $data = [
        'headers' => [
            'szlahu_error' => 'Some error message',
            'szlahu_error_code' => '57',
        ],
    ];

    $response = InvoiceResponse::parseData($data);

    expect($response->isError())->toBeTrue()
        ->and($response->getErrorMessage())->toBe('Some error message')
        ->and($response->getErrorCode())->toBe('57');
});

it('detects invoice notification send error', function () {
    $data = [
        'headers' => [
            'szlahu_szamlaszam' => 'TST-2025-001',
            'szlahu_error' => 'Notification failed',
            'szlahu_error_code' => '56',
        ],
    ];

    $response = InvoiceResponse::parseData($data);

    expect($response->hasInvoiceNotificationSendError())->toBeTrue()
        ->and($response->isError())->toBeFalse()
        ->and($response->hasInvoiceNumber())->toBeTrue();
});

it('throws on empty api response', function () {
    $apiResponse = new ApiResponse([], ResponseType::Text);
    $apiResponse->handleResponse();
})->throws(ResponseException::class);

it('throws on response without headers', function () {
    $rawResponse = [
        'headers' => [],
        'body' => 'some content',
    ];

    $apiResponse = new ApiResponse($rawResponse, ResponseType::Text);
    $apiResponse->handleResponse();
})->throws(ResponseException::class);

it('throws on system down response', function () {
    $rawResponse = [
        'headers' => [
            'szlahu_down' => 'true',
        ],
        'body' => 'maintenance',
    ];

    $apiResponse = new ApiResponse($rawResponse, ResponseType::Text);
    $apiResponse->handleResponse();
})->throws(ResponseException::class);

it('throws on empty body response', function () {
    $rawResponse = [
        'headers' => [
            'content-type' => 'text/html',
        ],
        'body' => '',
    ];

    $apiResponse = new ApiResponse($rawResponse, ResponseType::Text);
    $apiResponse->handleResponse();
})->throws(ResponseException::class);
