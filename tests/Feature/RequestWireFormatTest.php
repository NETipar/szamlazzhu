<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use NETipar\Szamlazzhu\Client;
use NETipar\Szamlazzhu\Document\Invoice;
use NETipar\Szamlazzhu\Document\Receipt;
use NETipar\Szamlazzhu\Document\ReverseInvoice;
use NETipar\Szamlazzhu\Enums\Currency;
use NETipar\Szamlazzhu\Enums\InvoiceTemplate;
use NETipar\Szamlazzhu\Enums\PaymentMethod;

function assertTagsInOrder(string $body, array $tags): bool
{
    $lastPosition = -1;

    foreach ($tags as $tag) {
        $position = strpos($body, "<{$tag}");

        if ($position === false || $position <= $lastPosition) {
            return false;
        }

        $lastPosition = $position;
    }

    return true;
}

function fakePdfInvoiceResponse(): void
{
    Http::fake([
        'www.szamlazz.hu/*' => Http::response('%PDF-1.4 fake', 200, [
            'szlahu_szamlaszam' => 'E-TESZT-2026-001',
            'szlahu_id' => '123456789',
            'Content-Type' => 'application/pdf',
        ]),
    ]);
}

it('keeps the invoice creation settings order on the wire', function () {
    fakePdfInvoiceResponse();

    $invoice = new Invoice;
    $invoice->getHeader()->setIssueDate('2026-02-09');
    $invoice->getHeader()->setPaymentMethod(PaymentMethod::BankTransfer);
    $invoice->getHeader()->setCurrency(Currency::HUF);

    app(Client::class)->generateInvoice($invoice);

    Http::assertSent(function ($request) {
        return assertTagsInOrder((string) $request->body(), [
            'beallitasok',
            'szamlaagentkulcs',
            'eszamla',
            'szamlaLetoltes',
            'szamlaLetoltesPld',
            'valaszVerzio',
            'aggregator',
        ]);
    });
});

it('keeps the reverse invoice settings order on the wire', function () {
    fakePdfInvoiceResponse();

    $invoice = new ReverseInvoice;
    $invoice->getHeader()->setInvoiceNumber('E-TESZT-2026-001');
    $invoice->getHeader()->setIssueDate('2026-02-09');

    app(Client::class)->generateReverseInvoice($invoice);

    Http::assertSent(function ($request) {
        return assertTagsInOrder((string) $request->body(), [
            'beallitasok',
            'szamlaagentkulcs',
            'eszamla',
            'szamlaLetoltes',
            'szamlaLetoltesPld',
            'aggregator',
            'valaszVerzio',
        ]);
    });
});

it('keeps the receipt creation settings on the wire', function () {
    $receiptXml = '<?xml version="1.0" encoding="UTF-8"?>'
        .'<xmlnyugtavalasz>'
        .'<sikeres>true</sikeres>'
        .'<nyugta><alap><id>100</id><nyugtaszam>NYGTA-2026-1</nyugtaszam></alap></nyugta>'
        .'</xmlnyugtavalasz>';

    Http::fake([
        'www.szamlazz.hu/*' => Http::response($receiptXml, 200, [
            'Content-Type' => 'application/octet-stream',
        ]),
    ]);

    $receipt = new Receipt;
    $receipt->getHeader()->setPrefix('NYGTA');
    $receipt->getHeader()->setPaymentMethod(PaymentMethod::Cash);
    $receipt->getHeader()->setCurrency(Currency::HUF);

    app(Client::class)->generateReceipt($receipt);

    Http::assertSent(function ($request) {
        return assertTagsInOrder((string) $request->body(), [
            'beallitasok',
            'szamlaagentkulcs',
            'pdfLetoltes',
            'fejlec',
            'elotag',
            'fizmod',
            'penznem',
        ]);
    });
});

it('sends simpleItems as the last invoice header element', function () {
    fakePdfInvoiceResponse();

    $invoice = new Invoice;
    $invoice->getHeader()->setIssueDate('2026-02-09');
    $invoice->getHeader()->setPaymentMethod(PaymentMethod::BankTransfer);
    $invoice->getHeader()->setCurrency(Currency::HUF);
    $invoice->getHeader()->setInvoiceTemplate(InvoiceTemplate::Default);
    $invoice->getHeader()->setPreviewPdf(true);
    $invoice->getHeader()->setSimpleItems(true);

    app(Client::class)->generateInvoice($invoice);

    Http::assertSent(function ($request) {
        $body = (string) $request->body();

        return assertTagsInOrder($body, ['fejlec', 'eusAfa', 'szamlaSablon', 'elonezetpdf', 'simpleItems'])
            && preg_match('/<simpleItems>(<!\[CDATA\[)?true(\]\]>)?<\/simpleItems>\s*<\/fejlec>/', $body) === 1;
    });
});
