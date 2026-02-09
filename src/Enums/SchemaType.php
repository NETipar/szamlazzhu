<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Enums;

enum SchemaType: string
{
    case Invoice = 'invoice';
    case Receipt = 'receipt';
    case Proforma = 'proforma';
    case TaxPayer = 'taxpayer';
}
