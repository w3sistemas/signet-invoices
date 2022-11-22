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

    CONST BANKS = [
        '237' => 'BANCO BRADESCO S.A',
        '341' => 'ITAÚ UNIBANCO S.A',
        '339' => 'BANCO SANTANDER (BRASIL) S.A',
        '707' => 'BANCO DAYCOVAL S.A.'
    ];
}
