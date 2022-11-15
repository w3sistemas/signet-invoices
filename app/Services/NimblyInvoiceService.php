<?php

namespace App\Services;

use App\Enumerators\NimblyEnum;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class NimblyInvoiceService
{
    /**
     * @var $client
     */
    private $http;

    public function __construct(Client $client)
    {
        $this->http = $client;
    }

    /**
     * @throws GuzzleException
     */
    public function createClient($params): string
    {
        try {
            $request = $this->http->post(env('NIMBLY_API') . NimblyEnum::PEOPLE, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'App' => env('NIMBLY_APP'),
                    'CN' => env('NIMBLY_CN')
                ],
                'json' => $params
            ]);

            return $request->getBody()->getContents();
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @throws GuzzleException
     */
    public function getClient($document): string
    {
        try {
            $request = $this->http->get(env('NIMBLY_API') . NimblyEnum::PEOPLE, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'App' => env('NIMBLY_APP'),
                    'CN' => env('NIMBLY_CN')
                ],
                'query' => [
                    'CPFCNPJ' => $document
                ]
            ]);

            return $request->getBody()->getContents();
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @throws GuzzleException
     */
    public function createInvoice($params): string
    {
        try {
            $request = $this->http->post(env('NIMBLY_API') . NimblyEnum::FINANCIAL_RECEPTION, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'App' => env('NIMBLY_APP'),
                    'CN' => env('NIMBLY_CN'),
                    'IDUsr' => env('NIMBLY_IDUSR'),
                    'Hash' => env('NIMBLY_HASH'),
                    'Usr' => env('NIMBLY_USR'),
                ],
                'json' => $params
            ]);

            return $request->getBody()->getContents();
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @throws GuzzleException
     */
    public function sentTicketData($params): string
    {
        try {
            $request = $this->http->post(env('NIMBLY_API_INVOICE') . NimblyEnum::SENT_TICKET_DATA, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'App' => env('NIMBLY_APP'),
                    'CN' => env('NIMBLY_CN'),
                    'IDUsr' => env('NIMBLY_IDUSR'),
                    'Hash' => env('NIMBLY_HASH'),
                    'Usr' => env('NIMBLY_USR'),
                ],
                'json' => $params
            ]);

            return $request->getBody()->getContents();
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
