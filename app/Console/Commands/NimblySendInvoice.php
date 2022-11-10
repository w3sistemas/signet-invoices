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
        $invoices = Invoice::select('customer_code')
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
                        $request = $this->nimblyInvoiceService->getClient($clientRemote->document);
                        if ($request) {
                            $client = Json::decode($request, 1);

                            if (!empty($client)) {
                                dd($client[0]['ID']);
                            }
                        }
                    }
                }
            }
        }
    }
}
