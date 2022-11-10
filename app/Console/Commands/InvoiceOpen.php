<?php

namespace App\Console\Commands;

use App\Enumerators\SignetEnum;
use App\Models\Invoice;
use App\Services\SignetInvoiceService;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\In;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class InvoiceOpen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:get-invoice-open';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Invoice Open';

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
    public function handle(): void
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $invoice = Invoice::select(DB::raw('invoice, month(invoice_date) month, year(invoice_date) year'))
            ->where('status', 'A')
            ->whereRaw("date_format(invoice_date, '%Y-%m') <> '{$year}-{$month}'")
            ->get();

        if ($invoice) {

            $numbers = [];
            $datas = [];

            foreach ($invoice as $number) {
                $numbers[] = $number['invoice'];
                $datas[] = [
                    'month' => $number['month'],
                    'year' => $number['year']
                ];
            }

            $datas = array_map("unserialize", array_unique(array_map("serialize", $datas)));

            foreach ($datas as $rowInvoice) {
                $params = [
                    'partner' => 'ABNCS',
                    'createdAt' => "01/{$rowInvoice['month']}/{$rowInvoice['year']}",
                    'status' => 'B'
                ];

                $request = $this->signetInvoiceService->getListInvoices($params, SignetEnum::INVOICES);

                if ($request) {

                    $output = Json::decode($request, 1);

                    foreach ($output['detail'] as $row) {

                        if (in_array($row['invoice'], $numbers)) {
                            $data = [
                                'abn' => 'ABNCS',
                                'invoice' => $row['invoice'],
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
                                'link_nfe' => $row['linkNFe']
                            ];

                            $invoice = Invoice::where([
                                'invoice' => $row['invoice'],
                                'status' => 'A'
                            ])
                                ->whereMonth('invoice_date', $rowInvoice['month'])
                                ->whereYear('invoice_date', $rowInvoice['year'])
                                ->first();

                            $invoice->update($data);
                        }
                    }
                }
            }
        }
    }
}
