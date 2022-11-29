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

class NimblySendInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:nimbly-send-invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nimbly Send Invoice';

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
            'send_nimbly' => 0,
            //'status' => 'A'
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
                         * Pessoa teste Local ou Pega Cliente da Base de Produção
                         * */
                        if (env('APP_ENV') === 'local') {
                            $idClient = 31;
                        } else {
                            $idClient = $client[0]['ID'];
                        }

                        /*
                         * Grava Rececimento
                         * */
                        $params = [
                            'ID' => 0,
                            'Descri' => setStringDescription($invoice['invoice_date'], $invoice['invoice_number']),
                            'DtaVenc' => $invoice['invoice_duedate'],
                            'VlrVenc' => $invoice['total'],
                            'VlrBruto' => $invoice['amount'],
                            'IDPessoa' => $idClient,
                            'IDCentroCusto' => $idClient['IDCentroCustoPreferencial'],
                            'DtaPagto' => $invoice['paid_date'],
                            'VlrPagto' => $invoice['paid'],
                            'DtaCompet' => $invoice['invoice_date'],
                            'IDTipoReceb' => 9, /*Banco CheckOK*/
                            'DataLimiteDesconto' => $invoice['invoice_duedate'],
                            'DataVencAtual' => $invoice['invoice_duedate'],
                            'NroDoc' => $invoice['invoice_number'],
                            'LinkNFSe' => $invoice['link_nfe']
                        ];

                        $request = $this->nimblyInvoiceService->createOrUpdateInvoice($params);

                        $dataInvoice = Json::decode($request, 1);

                        /*
                         * Grava Dados Boleto
                         * */
                        $rates = getRatesByBank($invoice['total']);

                        $ticketData = [
                            'ID' => 0,
                            'DataHoraEmissao' => $invoice['invoice_date'],
                            'DataVencimento' => $invoice['invoice_duedate'],
                            'Valor' => $invoice['total'],
                            'LinhaDigitavel' => $invoice['invoice_string'],
                            'IDContaRec' => $dataInvoice['ID'],
                            'NossoNumero' => $invoice['our_number'],
                            'NossoNumeroFormatado' => $invoice['our_number'],
                            'IDPessoaCedente' => 97,
                            'IDPessoaSacado' => $idClient,
                            'NumeroDocumento' => $invoice['invoice'],
                            'ValorJurosDiario' => $rates['day'] ?? 0,
                            'ValorMulta' => $rates['fine'] ?? 0,
                            'ValorDesconto' => $invoice['discounts'] ?? 0
                        ];

                        $request = $this->nimblyInvoiceService->sentTicketData($ticketData);

                        if ($request) {
                            $outputTicketData = Json::decode($request, 1);

                            $invoice->update([
                                'send_nimbly' => 1,
                                'send_nimbly_date' => Carbon::now()->toDateTimeString(),
                                'id_nimbly_invoice' => $dataInvoice['ID'],
                                'payload' => Json::encode($params),
                                'ticket_id' => $outputTicketData['ID'],
                            ]);
                        }
                    }
                }
            }
        }
    }
}
