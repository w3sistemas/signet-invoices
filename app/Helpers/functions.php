<?php

use App\Enumerators\SignetEnum;

function getRatesPeerBank(int $bank): array
{
    $rates = [];

    $bank = substr($bank, 0, 3);

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
