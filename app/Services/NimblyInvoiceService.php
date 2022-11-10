<?php

namespace App\Services;

use GuzzleHttp\Client;

class NimblyInvoiceService
{
    /**
     * @var Client
     */
    private $http;

    public function __construct(Client $client)
    {
        $this->http = $client;
    }
}
