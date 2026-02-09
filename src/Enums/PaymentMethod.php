<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Enums;

enum PaymentMethod: string
{
    case BankTransfer = 'átutalás';
    case Cash = 'készpénz';
    case CreditCard = 'bankkártya';
    case Check = 'csekk';
    case CashOnDelivery = 'utánvét';
    case PayPal = 'PayPal';
    case Szep = 'SZÉP kártya';
    case Otp = 'OTP Simple';
    case Compensation = 'kompenzáció';
    case Voucher = 'utalvány';
    case Barion = 'Barion';
    case Other = 'egyéb';
}
