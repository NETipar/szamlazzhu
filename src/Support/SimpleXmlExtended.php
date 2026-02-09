<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Support;

use SimpleXMLElement;

class SimpleXmlExtended extends SimpleXMLElement
{
    public function addCDataToNode(SimpleXMLElement $node, string $value = ''): void
    {
        $domElement = dom_import_simplexml($node);

        if ($domElement === false) {
            return;
        }

        $domOwner = $domElement->ownerDocument;

        if ($domOwner === null) {
            return;
        }

        $domElement->appendChild($domOwner->createCDATASection($value));
    }

    public function addChildWithCData(string $name, string $value = ''): SimpleXMLElement
    {
        $newChild = parent::addChild($name);

        if ($value !== '' && $newChild !== null) {
            $this->addCDataToNode($newChild, $value);
        }

        return $newChild;
    }

    public function addCData(string $value = ''): void
    {
        $this->addCDataToNode($this, $value);
    }

    #[\ReturnTypeWillChange]
    public function addChild(string $qualifiedName, ?string $value = null, ?string $namespace = null): ?static
    {
        return parent::addChild($qualifiedName, $value, $namespace);
    }

    public function extend(SimpleXMLElement $add): void
    {
        if ($add->count() !== 0) {
            $new = $this->addChild($add->getName());

            foreach ($add->children() as $child) {
                $new->extend($child);
            }
        } else {
            $new = $this->addChild($add->getName(), $this->cleanXmlNode($add));
        }

        foreach ($add->attributes() as $a => $b) {
            $new->addAttribute($a, (string) $b);
        }
    }

    public function remove(): static
    {
        $node = dom_import_simplexml($this);
        $node->parentNode->removeChild($node);

        return $this;
    }

    public function removeChild(SimpleXMLElement $child): static
    {
        $node = dom_import_simplexml($this);
        $childNode = dom_import_simplexml($child);
        $node->removeChild($childNode);

        return $this;
    }

    private function cleanXmlNode(SimpleXMLElement $data): string
    {
        $xmlString = $data->asXML();

        if ($xmlString === false) {
            return '';
        }

        if (str_contains($xmlString, '&')) {
            $cleanedXmlString = str_replace('&', '&amp;', $xmlString);
            $cleaned = simplexml_load_string($cleanedXmlString);

            return $cleaned !== false ? (string) $cleaned : (string) $data;
        }

        return (string) $data;
    }
}
