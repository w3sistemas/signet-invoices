<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\SignetInvoiceService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class Invoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:get-list-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get List Invoices';

    /**
     * @var SignetInvoiceService
     */
    private $signetInvoiceService;

    /**
     * Create a new command instance.
     *
     * @param SignetInvoiceService $signetInvoiceService
     */
    public function __construct(
        SignetInvoiceService $signetInvoiceService

    )
    {
        parent::__construct();
        $this->signetInvoiceService = $signetInvoiceService;
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws GuzzleException
     * @throws JsonException
     */
    public function handle()
    {
        $params = [
            'partner' => 'ABNCS',
            'createdAt' => "01/11/2022",
        ];

        $data = $this->signetInvoiceService->getListInvoices($params);

        if ($data) {

            $output = Json::decode($data, 1);

            foreach ($output['detail'] as $row) {
                Invoice::updateOrCreate(
                    [
                        'invoice' => $row['invoice'],
                    ],
                    [
                        'customer_code' => $row['customer_code'],
                        'company_name' => $row['company_name'],
                        'cnpj' => $row['cnpj'],
                        'status' => $row['status'],
                        'qty' => $row['qty'],
                        'amount' => $row['amount'],
                        'total' => $row['total'],
                        'paid' => $row['paid'],
                        'paid_date' => $row['paid_date'],
                        'invoice_date' => $row['invoice_date'],
                        'invoice_duedate' => $row['invoice_duedate'],
                        'invoice_number' => $row['invoice_number'],
                        'invoice_key' => $row['invoice_key'],
                        'invoice_string' => $row['invoice_string'],
                        'link_nfe' => $row['linkNFe'],
                    ]);
            }
        }
    }
}
