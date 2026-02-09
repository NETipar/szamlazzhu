<?php

return [
    'api_key' => env('SZAMLAZZHU_API_KEY', ''),
    'api_url' => env('SZAMLAZZHU_API_URL', 'https://www.szamlazz.hu/szamla/'),
    'download_pdf' => env('SZAMLAZZHU_DOWNLOAD_PDF', true),
    'response_type' => env('SZAMLAZZHU_RESPONSE_TYPE', 1),
    'timeout' => env('SZAMLAZZHU_TIMEOUT', 30),
    'connect_timeout' => env('SZAMLAZZHU_CONNECT_TIMEOUT', 0),
    'aggregator' => env('SZAMLAZZHU_AGGREGATOR', ''),
    'storage' => [
        'disk' => env('SZAMLAZZHU_STORAGE_DISK', 'local'),
        'pdf_path' => 'szamlazzhu/pdf',
        'xml_path' => 'szamlazzhu/xml',
    ],
    'save_pdf' => env('SZAMLAZZHU_SAVE_PDF', true),
    'save_request_xml' => env('SZAMLAZZHU_SAVE_REQUEST_XML', false),
    'save_response_xml' => env('SZAMLAZZHU_SAVE_RESPONSE_XML', false),
    'session' => [
        'driver' => env('SZAMLAZZHU_SESSION_DRIVER', 'cache'),
        'cache_store' => env('SZAMLAZZHU_SESSION_CACHE_STORE', null),
        'cache_prefix' => 'szamlazzhu_session_',
        'ttl' => 3600,
    ],
    'log_channel' => env('SZAMLAZZHU_LOG_CHANNEL', null),
    'certification_path' => env('SZAMLAZZHU_CERT_PATH', null),
];
