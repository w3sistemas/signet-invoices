<?php

namespace App\Enumerators;

class SignetEnum
{
    CONST INVOICES = 'invoices';

    CONST INVOICE_DETAIL = self::INVOICES . '/detail';

    CONST ABNS = [
        'ABN',
        'ABNCS',
        'ABN7',
        'SNCC'
    ];
}
