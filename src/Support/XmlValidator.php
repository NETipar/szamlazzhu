<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Support;

use DOMDocument;
use LibXMLError;

class XmlValidator
{
    /**
     * @return array<int, LibXMLError>
     */
    public function checkValidXml(string $xmlContent): array
    {
        $previousState = libxml_use_internal_errors(true);

        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->loadXML($xmlContent);

        $errors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($previousState);

        return $errors;
    }
}
