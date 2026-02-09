<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Document;

use NETipar\Szamlazzhu\Header\DocumentHeader;
use NETipar\Szamlazzhu\Item\Item;

abstract class Document
{
    /** @var Item[] */
    public array $items = [];

    abstract public function getHeader(): DocumentHeader;

    public function addItem(Item $item): static
    {
        $this->items[] = $item;

        return $this;
    }

    /** @param Item[] $items */
    public function setItems(array $items): static
    {
        $this->items = $items;

        return $this;
    }

    /** @param array<string, mixed> $settings */
    abstract public function toXmlArray(array $settings): array;

    protected function buildXmlItemsData(): array
    {
        $data = [];

        foreach ($this->items as $key => $item) {
            $data["item{$key}"] = $item->toXmlArray();
        }

        return $data;
    }
}
