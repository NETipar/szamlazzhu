<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Exceptions;

use RuntimeException;

class SzamlazzhuException extends RuntimeException
{
    public const string SYSTEM_DOWN = 'Az oldal jelenleg karbantartás alatt áll. Kérjük, látogass vissza pár perc múlva.';

    public const string REQUEST_TYPE_NOT_EXISTS = 'A kérés típusa nem létezik';

    public const string RESPONSE_TYPE_NOT_EXISTS = 'A válasz típusa nem létezik';

    public const string XML_SCHEMA_TYPE_NOT_EXISTS = 'Az XML séma típusa nem létezik';

    public const string XML_KEY_NOT_EXISTS = 'XML kulcs nem létezik';

    public const string XML_NOT_VALID = 'Az összeállított XML nem érvényes';

    public const string XML_DATA_NOT_AVAILABLE = 'Hiba történt az XML adatok összeállításánál: nincs adat.';

    public const string XML_DATA_BUILD_FAILED = 'Az XML adatok összeállítása sikertelen';

    public const string FIELDS_CHECK_ERROR = 'Hiba a mezők ellenőrzése közben';

    public const string DATE_FORMAT_NOT_EXISTS = 'Nincs ilyen dátum formátum';

    public const string NO_SZLAHU_KEY_IN_HEADER = 'Érvénytelen válasz!';

    public const string DOCUMENT_DATA_IS_MISSING = 'A bizonylat PDF adatai hiányoznak!';

    public const string PDF_FILE_SAVE_SUCCESS = 'PDF fájl mentése sikeres';

    public const string PDF_FILE_SAVE_FAILED = 'PDF fájl mentése sikertelen';

    public const string AGENT_RESPONSE_NO_CONTENT = 'A Számla Agent válaszában nincs tartalom!';

    public const string AGENT_RESPONSE_NO_HEADER = 'A Számla Agent válasza nem tartalmaz fejlécet!';

    public const string AGENT_RESPONSE_IS_EMPTY = 'A Számla Agent válasza nem lehet üres!';

    public const string AGENT_ERROR = 'Agent hiba';

    public const string FILE_CREATION_FAILED = 'A fájl létrehozása sikertelen.';

    public const string ATTACHMENT_NOT_EXISTS = 'A csatolandó fájl nem létezik';

    public const string INVOICE_NOTIFICATION_SEND_FAILED = 'Számlaértesítő kézbesítése sikertelen';

    public const string INVALID_JSON = 'Érvénytelen JSON';

    public const string INVOICE_EXTERNAL_ID_IS_EMPTY = 'A külső számlaazonosító üres';

    public const string CONNECTION_ERROR = 'Sikertelen kapcsolódás';

    public const string RESPONSE_XML_PARSE_FAILED = 'A válasz XML feldolgozása sikertelen';

    public const string XML_FILE_SAVE_FAILED = 'XML fájl mentése sikertelen';

    public const string MISSING_CERTIFICATION_FILE = 'A megadott certifikációs fájl nem létezik';
}
