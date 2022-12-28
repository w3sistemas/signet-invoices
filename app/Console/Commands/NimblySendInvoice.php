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
            'send_nimbly' => 0
        ])
            ->get();

        if ($invoices) {
            foreach ($invoices as $invoice){

                $request = $this->nimblyInvoiceService->getClient($invoice['cnpj']);

                if ($request) {
                    $client = Json::decode($request, 1);

                    if (!empty($client)) {
                        /*
                         * Grava Rececimento
                         * */
                        $params = [
                            'ID' => 0,
                            'Descri' => setStringDescription($invoice['invoice_date'], $invoice['invoice_number']),
                            'DtaVenc' => $invoice['invoice_duedate'],
                            'VlrVenc' => $invoice['ticket_value'],
                            'VlrBruto' => $invoice['amount'],
                            'IDPessoa' => $client[0]['ID'],
                            'IDCentroCusto' => $client[0]['IDCentroCustoPreferencial'],
                            'DtaPagto' => $invoice['paid_date'],
                            'VlrPagto' => $invoice['paid'],
                            'DtaCompet' => $invoice['invoice_date'],
                            'IDTipoReceb' => getReceiptTypeByBank($invoice['bank']),
                            'DataLimiteDesconto' => $invoice['invoice_duedate'],
                            'DataVencAtual' => $invoice['invoice_duedate'],
                            'NroDoc' => $invoice['invoice_number'],
                            'LinkNFSe' => $invoice['link_nfe']
                        ];

                        $request = $this->nimblyInvoiceService->createOrUpdateInvoice($params);

                        $dataInvoice = Json::decode($request, 1);

                        if ($request) {
                            $invoice->update([
                                'send_nimbly' => 1,
                                'send_nimbly_date' => Carbon::now()->toDateTimeString(),
                                'id_nimbly_invoice' => $dataInvoice['ID'],
                                'payload' => Json::encode($params)
                            ]);
                        }
                    }
                }
            }
        }
    }
}
