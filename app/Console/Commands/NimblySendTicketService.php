<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Invoice;
use App\Services\NimblyInvoiceService;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class NimblySendTicketService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:nimbly-send-ticket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nimbly Send Ticket';

    /**
     * @var NimblyInvoiceService
     */
    private $nimblyInvoiceService;

    /**
     * Create a new command instance.
     *
     * @param NimblyInvoiceService $nimblyInvoiceService
     */
    public function __construct(
        NimblyInvoiceService $nimblyInvoiceService

    )
    {
        parent::__construct();
        $this->nimblyInvoiceService = $nimblyInvoiceService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws GuzzleException
     * @throws JsonException
     */
    public function handle(): void
    {
        $invoices = Invoice::where([
            'send_nimbly' => 1,
            'send_email' => 0,
            'send_nimbly_ticket' => 0
        ])
            ->whereNotNull('invoice_string')
            ->get();

        if ($invoices) {
            foreach ($invoices as $invoice){

                $request = $this->nimblyInvoiceService->getClient($invoice['cnpj']);

                if ($request) {
                    $client = Json::decode($request, 1);

                    if (!empty($client)) {
                        /*
                         * Grava Dados Boleto
                         * */
                        $rates = getRatesByBank($invoice['bank']);

                        $ticketData = [
                            'ID' => 0,
                            'DataHoraEmissao' => $invoice['invoice_date'],
                            'DataVencimento' => $invoice['invoice_duedate'],
                            'Valor' => $invoice['ticket_value'],
                            'LinhaDigitavel' => $invoice['invoice_string'],
                            'IDContaRec' => $invoice['id_nimbly_invoice'],
                            'NossoNumero' => $invoice['our_number'],
                            'NossoNumeroFormatado' => $invoice['our_number'],
                            'IDPessoaCedente' => 97,
                            'IDPessoaSacado' => $client[0]['ID'],
                            'NumeroDocumento' => $invoice['invoice'],
                            'ValorJurosDiario' => $rates['day'] ?? 0,
                            'ValorMulta' => $rates['fine'] ?? 0,
                            'ValorDesconto' => $invoice['discounts'] ?? 0
                        ];

                        $request = $this->nimblyInvoiceService->sentTicketData($ticketData);

                        if ($request) {

                            $this->nimblyInvoiceService->sendEmail($invoice['id_nimbly_invoice']);

                            $outputTicketData = Json::decode($request, 1);

                            $invoice->update([
                                'send_nimbly_ticket' => 1,
                                'ticket_id' => $outputTicketData['ID'],
                                'send_email' => 1,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
