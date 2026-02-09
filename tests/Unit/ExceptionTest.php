<?php

use NETipar\Szamlazzhu\Exceptions\ConnectionException;
use NETipar\Szamlazzhu\Exceptions\ResponseException;
use NETipar\Szamlazzhu\Exceptions\SzamlazzhuException;
use NETipar\Szamlazzhu\Exceptions\ValidationException;
use NETipar\Szamlazzhu\Exceptions\XmlBuildException;

it('exception hierarchy extends RuntimeException', function () {
    $exception = new SzamlazzhuException('test');

    expect($exception)->toBeInstanceOf(\RuntimeException::class);
});

it('connection exception extends base exception', function () {
    $exception = new ConnectionException('connection failed');

    expect($exception)->toBeInstanceOf(SzamlazzhuException::class)
        ->and($exception->getMessage())->toBe('connection failed');
});

it('xml build exception extends base exception', function () {
    $exception = new XmlBuildException('xml error');

    expect($exception)->toBeInstanceOf(SzamlazzhuException::class);
});

it('response exception extends base exception', function () {
    $exception = new ResponseException('response error');

    expect($exception)->toBeInstanceOf(SzamlazzhuException::class);
});

it('validation exception extends base exception', function () {
    $exception = new ValidationException('validation error');

    expect($exception)->toBeInstanceOf(SzamlazzhuException::class);
});

it('has all required error message constants', function () {
    expect(SzamlazzhuException::SYSTEM_DOWN)->toBeString()
        ->and(SzamlazzhuException::CONNECTION_ERROR)->toBeString()
        ->and(SzamlazzhuException::XML_NOT_VALID)->toBeString()
        ->and(SzamlazzhuException::AGENT_RESPONSE_IS_EMPTY)->toBeString()
        ->and(SzamlazzhuException::AGENT_RESPONSE_NO_HEADER)->toBeString()
        ->and(SzamlazzhuException::AGENT_RESPONSE_NO_CONTENT)->toBeString()
        ->and(SzamlazzhuException::AGENT_ERROR)->toBeString()
        ->and(SzamlazzhuException::PDF_FILE_SAVE_SUCCESS)->toBeString()
        ->and(SzamlazzhuException::PDF_FILE_SAVE_FAILED)->toBeString();
});
