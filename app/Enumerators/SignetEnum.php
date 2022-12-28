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

    CONST BRADESCO = 237; //11

    CONST ITAU = 341; //9

    CONST SANTANDER = 339; //12

    CONST DAYCOVAL = 707; //13
}
