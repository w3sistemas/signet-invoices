<?php

namespace App\Services;

use App\Enumerators\SignetEnum;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Psy\Util\Json;

class SignetInvoiceService
{
    /**
     * @var Client
     */
    private $http;

    public function __construct(Client $client)
    {
        $this->http = $client;
    }

    /**
     * @throws GuzzleException
     */
    public function getListInvoices($params, $route): string
    {
        try {
            $request = $this->http->post(env('SIGNET_API') . $route, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'x-access-token' => env('SIGNET_API_KEY')
                ],
                'json' => $params
            ]);

            return $request->getBody()->getContents();
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
