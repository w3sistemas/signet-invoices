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
            ->limit(1)
            ->get();

        $idClient = null;

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

                        $idClient = $clientNimbly['ID'];

                        $clientRemote->id_nimbly = $clientNimbly['ID'];
                        $clientRemote->save();

                    } else {
                        $request = $this->nimblyInvoiceService->getClient($clientRemote->document);
                        if ($request) {
                            $client = Json::decode($request, 1);

                            if (!empty($client)) {
                                $idClient = $client[0]['ID'];
                            }
                        }
                    }
                }

                $params = [
                    '_DataVencimento' => $invoice['invoice_duedate'],
                    'ID' => 0,
                    'Descri' => 'string',
                    'DtaVenc' => $invoice['invoice_duedate'],
                    'VlrVenc' => $invoice['total'],
                    'VlrBruto' => $invoice['amount'],
                    'IDPessoa' => $idClient,
                    'Pessoa' => [
                        'ID' => $idClient,
                        'NomeExibicao' => $clientRemote->corporate_name,
                        'Nome' => $clientRemote->corporate_name,
                        'CPFCNPJ' => $clientRemote->document,
                        'TelefonePrincipal' => $clientRemote->phone,
                        'Cidade' => [
                            'ID' => 0,
                            'Nome' => $clientRemote->city,
                            'UF' => $clientRemote->state,
                            'CodIBGE' => 0,
                            'AliquotaISS' => 0,
                            'IDGoiania' => 0,
                            'IDPais' => 0
                        ]
                    ],
                    'IDMoeda' => 0,
                    'IDCentroCusto' => 0,
                    'IDPlanoContaOrigem' => 'string',
                    'Obs' => 'string',
                    'IDContaBanco' => 0,
                    'IDBancoRecebimento' => 0,
                    '_DataPagamento' => $invoice['invoice_date'],
                    'DtaPagto' => $invoice['invoice_date'],
                    'VlrPagto' => $invoice['paid'],
                    'IDPlanoContaPagto' => 'string',
                    'IDMoedaCaixa' => 0,
                    '_DataCompetencia' => $invoice['invoice_date'],
                    'DtaCompet' => $invoice['invoice_date'],
                    'IDTipoReceb' => 0,
                    'PercJurosDiario' => 0,
                    'PercMultaAtraso' => 0,
                    'VlrDesc' => 0,
                    'DataLimiteDesconto' => $invoice['invoice_duedate'],
                    'IDModeloRelatorioBoleto' => 0,
                    'IDNotaFiscal' => $invoice['invoice_number'],
                    'IDNotaFiscalProduto' => $invoice['invoice_number'],
                    'NroDoc' => $invoice['invoice'],
                    'IDContrato' => 0,
                    'SitConta' => 0,
                    'DescricaoSituacao' => 'string',
                    'DataVencAtual' => $invoice['invoice_duedate'],
                    'IDVenda' => 0,
                    'IDBoleto' => $invoice['invoice'],
                    'NossoNumeroBoleto' => 'string',
                    'Detalhamento' => 'string',
                    'IDUsuarioInclusao' => 0,
                    'DataHoraInclusao' => Carbon::now()->toDateTimeString(),
                    'IDExtratoBanco' => 0,
                    'IDConciliacaoBancaria' => 0,
                    'IDReguaCobranca' => 0,
                    'NomePessoa' => $clientRemote->corporate_name,
                    'CPFCNPJ' => $clientRemote->document,
                    'NomeCentroCusto' => 'string',
                    'NomeContaOrigem' => 'string',
                    'NomeContaDestino' => 'string',
                    'NomeTipoReceb' => 'string',
                    'NroNFSe' => 'string',
                    'NroNFe' => $invoice['invoice_number'],
                    'SimbMoeda' => 'R$',
                    'IDNFSe' => 0,
                    'ChaveAcessoNFSe' => 'string',
                    'LinkNFSe' => $invoice['link_nfe'],
                    'LinkBoleto' => 'string',
                    'LinhaDigitavelBoleto' => $invoice['invoice_string'],
                    'ChaveAcessoExterno' => 'string',
                    'CodigoExterno' => 'string',
                    'IDFaturaContrato' => 0,
                    'IDPessoaTokenCartaoCredito' => 0,
                    'ObjetoGateway' => 'string',
                    'IDContaReceberOrigem' => 0,
                    'PessoaEmissor' => null,
                    'PessoaDevedor' => null,
                    'VlrJuros' => 0,
                    'VlrTaxa' => 0,
                    'VlrDescReceb' => 0
                ];

                $request = $this->nimblyInvoiceService->createInvoice($params);

                $dataInvoice = Json::decode($request, 1);

                $invoice->update([
                    'send_nimbly' => 1,
                    'send_nimbly_date' => Carbon::now()->toDateTimeString(),
                    'id_nimbly_invoice' => $dataInvoice['ID']
                ]);
            }
        }
    }
}
