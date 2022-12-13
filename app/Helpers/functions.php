<?php

use App\Enumerators\SignetEnum;
use Carbon\Carbon;

function getRatesByBank(int $bank): array
{
    $rates = [];

    switch ($bank) {
        case SignetEnum::DAYCOVAL:
        case SignetEnum::BRADESCO:
            $rates = [
                'fine' => 2,
                'month' => 10,
                'day' => 0.33
            ];
            break;
        case SignetEnum::ITAU:
            $rates = [
                'fine' => null,
                'month' => 5.9,
                'day' => 0.20
            ];
            break;
        case SignetEnum::SANTANDER:
            $rates = [
                'fine' => 2,
                'month' => 1,
                'day' => 0.03
            ];
            break;
    }

    return $rates;
}


/*
 * Alterar para mÊs de consumo
 * */
function setStringDescription($invoiceDate, $invoiceNumber): string
{
    $first = Carbon::createFromFormat('Y-m-d h:i:s', $invoiceDate)->subMonth()
        ->firstOfMonth()
        ->format('d/m/Y');

    $end = Carbon::createFromFormat('Y-m-d h:i:s', $invoiceDate)->subMonth()
        ->endOfMonth()
        ->format('d/m/Y');

    return 'NF: ' . $invoiceNumber . ' | Período ( ' . $first . ' - ' . $end . ' ) ';
}
