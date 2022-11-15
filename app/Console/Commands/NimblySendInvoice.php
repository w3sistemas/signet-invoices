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
                    'ID' => 0,
                    'Descricao' => '[Nf 2479] Parcela 2',
                    'Obs' => '
1 x 4100615 Antena Gps 1500 Ag Leader - 128
 Uni: R$83.33 | Total: R$83.33
1 x 4002673-18 Chicote Eletrico Ag Leader - 311
 Uni: R$142.86 | Total: R$142.86
1 x 4100858 Display Cable Kit - Can a W-22ft Power Ag Leader - 580
 Uni: R$714.29 | Total: R$714.29
1 x 41000252 Display Kit - Compass Ag Leader - 584
 Uni: R$4761.9 | Total: R$4761.9',
                    'ValorBruto' => 3000,
                    'ValorVencimento' => 3000,
                    'ValorPagamento' => 3000,
                    'DataCompetencia' => '2016-07-28T10:53:00',
                    'DataVencimento' => '2016-09-25T00:00:00',
                    'DataPagamento' => '2016-09-20T00:00:00',
                    'DataAgendaPagamento' => null,
                    'IDPlanoContaClassificacao' => '40106          ',
                    'ContaClassificacao' => [
                        'ID' => '40106          ',
                        'Nome' => 'Materiais de Limpeza',
                        'Hierarquia' => null
                    ],
                    'IDPlanoContaPagamento' => '10103          ',
                    'ContaPagamento' => [
                        'ID' => '10103          ',
                        'Nome' => 'Caixa Local',
                        'Hierarquia' => null
                    ],
                    'IDCentroCusto' => 1,
                    'CentroCusto' => [
                        'ID' => 1,
                        'NomeExibicao' => 'Administrativo',
                        'Hierarquia' => null
                    ],
                    'IDPessoa' => $idClient,
                    'Pessoa' => [
                        'ID' => $idClient,
                        'NomeExibicao' => $clientRemote->corporate_name,
                        'Nome' => $clientRemote->corporate_name,
                        'CPFCNPJ' => $clientRemote->document,
                        'IDCentroCusto' => null,
                        'CentroCusto' => null
                    ],
                    'FormaPagamento' => 99,
                    'DescricaoFormaPagamento' => 'Outros',
                    'NroBoleto' => null,
                    'IDMoeda' => 1,
                    'NroNotaFiscal' => 2479,
                    'DataHoraInclusao' => '2020-04-13T14:04:56.17',
                    'IDUsuarioInclusao' => 1,
                    'DataHoraSituacao' => '2020-05-14T15:28:57.323',
                    'IDUsuarioSituacao' => 1,
                    'Situacao' => 2,
                    'ArquivoGerado' => false,
                    'DataArquivoGerado' => null,
                    'IDTabelaImposto' => null,
                    'IDTipoImposto' => null,
                    'IDExtratoBanco' => null,
                    'IDConciliacaoBancaria' => null,
                    'NomePessoa' => 'Gti',
                    'NomeCentroCusto' => 'Administrativo',
                    'NomeContaClassificacao' => 'Materiais de Limpeza',
                    'NomeContaPagamento' => 'Caixa Local',
                    'SimbMoeda' => 'R$',
                    'CodigoBancoPessoa' => null,
                    'NomeBancoPessoa' => null,
                    'AgenciaPessoa' => null,
                    'ContaCorrentePessoa' => null,
                    'OperacaoContaPessoa' => null,
                    'CPFCNPJPessoa' => $clientRemote->document,
                    'IDProjeto' => null,
                    'Projeto' => null,
                    'TransacaoExtrato' => null
                ];

                $request = $this->nimblyInvoiceService->createInvoice($params);

                $invoice->update(['send_nimbly' => 1]);

                dd($request);

            }
        }
    }
}
