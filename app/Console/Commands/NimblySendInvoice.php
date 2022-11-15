<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Invoice;
use App\Services\NimblyInvoiceService;
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
        $invoices = Invoice::select('id', 'customer_code', 'send_nimbly')
            ->where([
            'send_nimbly' => 0
        ])
            ->limit(1)
            ->get();

        if ($invoices) {
            foreach ($invoices as $invoice){
                $clientRemote = Client::where('code', $invoice['customer_code'])->first();
                if ($clientRemote) {
                    if (!$clientRemote->id_nimbly) {

                        $params = [
                            'Nome' => $clientRemote->corporate_name,
                            'NomeFantasia' => $clientRemote->corporate_name,
                            'NomeExibicao' => $clientRemote->corporate_name,
                            'CPFCNPJ' => $clientRemote->document,
                            'DataNascimentoFundacao' => null,
                            'IE' => null,
                            'Logradouro' => $clientRemote->street,
                            'Nro' => $clientRemote->number,
                            'Bairro' => $clientRemote->district,
                            'CEP' => $clientRemote->zipcode,
                            'Cidade' => $clientRemote->city,
                            'UF' => $clientRemote->state,
                            'Email' => $clientRemote->email,
                            'Telefone' => (!empty($clientRemote->area_code) ? $clientRemote->area_code . $clientRemote->phone : null),
                            'Celular' => null,
                            'Cliente' => true
                        ];

                        $request = $this->nimblyInvoiceService->createClient($params);

                        $clientNimbly = Json::decode($request, 1);

                        $clientRemote->id_nimbly = $clientNimbly['ID'];
                        $clientRemote->save();

                    } else {
                        $request = $this->nimblyInvoiceService->getClient($clientRemote->document);
                        if ($request) {
                            $client = Json::decode($request, 1);

                            if (!empty($client)) {
                                $invoice->update(['send_nimbly' => 1]);
                                dd($client[0]['ID']);
                            }
                        }
                    }
                }
            }
        }
    }
}
