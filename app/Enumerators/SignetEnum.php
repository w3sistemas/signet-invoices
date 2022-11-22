<?php

namespace App\Enumerators;

class SignetEnum
{
    CONST INVOICES = 'invoices';

    CONST INVOICE_DETAIL = self::INVOICES . '/detail';

    CONST ABNS = [
        'ABNCS',
        'SNCC',
        'SCP',
        'ABN',
        'ABN7'
    ];

    CONST BRADESCO = 237;

    CONST ITAU = 341;

    CONST SANTANDER = 339;

    CONST DAYCOVAL = 707;
}
