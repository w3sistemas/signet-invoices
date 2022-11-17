<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\SentInvoice;
use App\Services\NimblyInvoiceService;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class NimblyUpdateInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:nimbly-update-invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nimbly Update Invoice';

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
        $invoices = SentInvoice::where([
            'sent' => 0
        ])->get();

        if ($invoices) {
            foreach ($invoices as $invoice){

                $params = [
                    'ID' => $invoice['id_nimbly_invoice'],
                    'DtaPagto' => $invoice['paid_date'],
                    'VlrPagto' => $invoice['paid']
                ];

                $request = $this->nimblyInvoiceService->createOrUpdateInvoice($params);

                dd($request);

                if ($request) {
                    $invoice->update([
                        'sent' => 1
                    ]);
                }
            }
        }
    }
}
