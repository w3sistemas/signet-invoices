<?php

use App\Enumerators\SignetEnum;

function getDiscountPeerBank(int $bank): array
{
    $fees = [];

    $bank = substr($bank, 0, 3);

    switch ($bank) {
        case SignetEnum::DAYCOVAL:
        case SignetEnum::BRADESCO:
            $fees = [
                'fine' => 2,
                'month' => 10,
                'day' => 0.33
            ];
            break;
        case SignetEnum::ITAU:
            $fees = [
                'fine' => null,
                'month' => 5.9,
                'day' => 0.20
            ];
            break;
        case SignetEnum::SANTANDER:
            $fees = [
                'fine' => 2,
                'month' => 1,
                'day' => 0.03
            ];
            break;
    }

    return $fees;
}
