<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Exceptions;

class SzamlazzhuException extends \RuntimeException
{
    public const SYSTEM_DOWN = 'Az oldal jelenleg karbantartás alatt áll. Kérjük, látogass vissza pár perc múlva.';

    public const REQUEST_TYPE_NOT_EXISTS = 'A kérés típusa nem létezik';

    public const RESPONSE_TYPE_NOT_EXISTS = 'A válasz típusa nem létezik';

    public const XML_SCHEMA_TYPE_NOT_EXISTS = 'Az XML séma típusa nem létezik';

    public const XML_KEY_NOT_EXISTS = 'XML kulcs nem létezik';

    public const XML_NOT_VALID = 'Az összeállított XML nem érvényes';

    public const XML_DATA_NOT_AVAILABLE = 'Hiba történt az XML adatok összeállításánál: nincs adat.';

    public const XML_DATA_BUILD_FAILED = 'Az XML adatok összeállítása sikertelen';

    public const FIELDS_CHECK_ERROR = 'Hiba a mezők ellenőrzése közben';

    public const DATE_FORMAT_NOT_EXISTS = 'Nincs ilyen dátum formátum';

    public const NO_SZLAHU_KEY_IN_HEADER = 'Érvénytelen válasz!';

    public const DOCUMENT_DATA_IS_MISSING = 'A bizonylat PDF adatai hiányoznak!';

    public const PDF_FILE_SAVE_SUCCESS = 'PDF fájl mentése sikeres';

    public const PDF_FILE_SAVE_FAILED = 'PDF fájl mentése sikertelen';

    public const AGENT_RESPONSE_NO_CONTENT = 'A Számla Agent válaszában nincs tartalom!';

    public const AGENT_RESPONSE_NO_HEADER = 'A Számla Agent válasza nem tartalmaz fejlécet!';

    public const AGENT_RESPONSE_IS_EMPTY = 'A Számla Agent válasza nem lehet üres!';

    public const AGENT_ERROR = 'Agent hiba';

    public const FILE_CREATION_FAILED = 'A fájl létrehozása sikertelen.';

    public const ATTACHMENT_NOT_EXISTS = 'A csatolandó fájl nem létezik';

    public const INVOICE_NOTIFICATION_SEND_FAILED = 'Számlaértesítő kézbesítése sikertelen';

    public const INVALID_JSON = 'Érvénytelen JSON';

    public const INVOICE_EXTERNAL_ID_IS_EMPTY = 'A külső számlaazonosító üres';

    public const CONNECTION_ERROR = 'Sikertelen kapcsolódás';

    public const XML_FILE_SAVE_FAILED = 'XML fájl mentése sikertelen';

    public const MISSING_CERTIFICATION_FILE = 'A megadott certifikációs fájl nem létezik';
}
