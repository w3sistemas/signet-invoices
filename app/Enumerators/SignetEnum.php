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
}
