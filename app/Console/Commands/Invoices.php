<?php

namespace App\Console\Commands;

use App\Services\SignetInvoiceService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;

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
     */
    public function handle()
    {
        $params = [
            'partner' => 'ABNCS',
            'createdAt' => "01/11/2022",
        ];

        $data = $this->signetInvoiceService->getListInvoices($params);

        dd($data);
    }
}
