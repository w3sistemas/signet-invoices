<?php

namespace App\Console\Commands;

use App\Enumerators\SignetEnum;
use App\Models\Invoice;
use App\Services\SignetInvoiceService;
use Carbon\Carbon;
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
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        foreach (SignetEnum::ABNS as $abn) {
            $params = [
                'partner' => $abn,
                'createdAt' => "01/{$month}/{$year}"
            ];

            $request = $this->signetInvoiceService->getListInvoices($params, SignetEnum::INVOICES);

            if ($request) {

                $output = Json::decode($request, 1);

                foreach ($output['detail'] as $row) {

                    $dataCreate = [
                        'abn' => $abn,
                        'invoice' => $row['invoice'],
                        'customer_code' => $row['customer_code'],
                        'company_name' => $row['company_name'],
                        'cnpj' => $row['cnpj'],
                        'status' => $row['status'],
                        'qty' => $row['qty'],
                        'amount' => $row['amount'],
                        'total' => $row['total'],
                        'ticket_value' => getTicketValue($row['invoice_string']) ?? null,
                        'paid' => $row['paid'],
                        'paid_date' => $row['paid_date'],
                        'invoice_date' => $row['invoice_date'],
                        'invoice_duedate' => $row['invoice_duedate'],
                        'invoice_number' => $row['invoice_number'],
                        'invoice_key' => $row['invoice_key'],
                        'invoice_string' => $row['invoice_string'],
                        'our_number' => $row['our_number'],
                        'late_payment' => $row['late_payment'],
                        'bank' => $row['bank'],
                        'discounts' => $row['discounts'],
                        'increase' => $row['increase'],
                        'link_nfe' => $row['linkNFe']
                    ];

                    $dataUpdate = [
                        'status' => $row['status'],
                        'paid' => $row['paid'],
                        'paid_date' => $row['paid_date']
                    ];

                    $invoice = Invoice::where('invoice',  $row['invoice'])
                        ->whereMonth('invoice_date', $month)
                        ->whereYear('invoice_date', $year)
                        ->first();

                    if ($invoice) {
                        $invoice->update($dataUpdate);
                    } else {
                        Invoice::create($dataCreate);
                    }
                }
            }
        }
    }
}
