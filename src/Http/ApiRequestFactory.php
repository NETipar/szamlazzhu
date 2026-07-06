<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Http;

use NETipar\Szamlazzhu\Session\SessionManager;
use NETipar\Szamlazzhu\Support\XmlBuilder;
use NETipar\Szamlazzhu\Support\XmlValidator;

class ApiRequestFactory
{
    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, string>  $customHeaders
     */
    public function make(array $config, SessionManager $sessionManager, array $customHeaders = []): ApiRequest
    {
        return new ApiRequest($config, new XmlBuilder, new XmlValidator, $sessionManager, $customHeaders);
    }
}
