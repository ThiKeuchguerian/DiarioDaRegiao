<?php
require_once __DIR__ . '/../DBConnect.php';

class IntegracaoClientes
{
  private $gi;
  private $capt;
  private $senior;
  private $gestor;

  public function __construct()
  {
    $this->gi = DatabaseConnection::getConnection('gi');
    $this->capt = DatabaseConnection::getConnection('capt');
    $this->gestor = DatabaseConnection::getConnection('gestor');
    $this->senior = DatabaseConnection::getConnection('senior');

    $this->senior->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
  }
  /**
   * Extrai primeiro e último dia no formato YYYYMMDD a partir de 'MM/YYYY'
   */
  public static function obterPrimeiroUltimoDia(string $MesAno): array
  {
    $data = \DateTime::createFromFormat('m/Y', $MesAno);
    if (! $data) {
      throw new \InvalidArgumentException("Mês/Ano inválido: {$MesAno}");
    }
    $primeiro = $data->format('Ym01');               // YYYY-mm-dd
    $ultimo    = $data->modify('last day of this month')->format('Ymd');
    // echo "<pre>";
    // var_dump($primeiro, $ultimo);
    // die();
    return [$primeiro, $ultimo];
  }
  function limparCpfCnpj($dados)
  {
    return preg_replace('/[^0-9]/', '', $dados);
  }

  function consultaClientesGi($dados): array
  {
    $nomeCli = $dados['nomeCli'] ?? '';
    $cpfCnpj = $dados['cpfCnpj'] ?? '';

    $sql =
      "SELECT
        cli.codfavorec, cli.cgccpf, cli.ierg, cli.razao, cli.apelido, cli.email, cli.emaildest, cli.emailcobranca,
        cli.ddd, cli.fone1, cli.fone2,
        cli.endereco, cli.numero, cli.complemento, cli.bairro, cli.cep, cli.cidade, cli.estado,
        cli.indicacao, cli.ativo, cli.dti,
        f.codint, f.comisvend, f.comisagen, f.limcredito,
        con.nome as Contato, con.email as EmailContato
        FROM fv_end cli
        inner join fv_favor f on cli.codfavorec = f.codfavorec
        left outer join fv_cont con on cli.codfavorec = con.codfavorec
      ";

    $where = [];
    $params = [];

    if (!empty($cpfCnpj)) {
      $where[] = "cli.cgccpf = :cpfCnpj";
      $params[':cpfCnpj'] = $cpfCnpj;
    }
    if (!empty($nomeCli)) {
      $where[] = "cli.razao LIKE :nomeCli";
      $params[':nomeCli'] = '%' . $nomeCli . '%';
    }

    if ($where) {
      $sql .= "\n WHERE " . implode(' AND ', $where);
    }

    $sql .= "\n ORDER BY cli.razao";
    $stmt = $this->gi->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  function consultaClientesCapt($dados): array
  {
    $nomeCli = $dados['nomeCli'] ?? '';
    $cpfCnpj = $dados['cpfCnpj'] ?? '';

    $sql =
      "SELECT c.codCliente, c.razaoSocial, c.nomeFantasia, c.cpfCnpj, c.inscrEstadual, 
          c.telefoneFixoDDD, c.telefoneFixoNro, c.celularDDD, c.celularNro, 
          e.logradouro, e.nro, e.cep, e.complemento, e.bairro, e.cidade, e.codMunicipio,
          ec.logradouro AS endcob, ec.nro AS nroCob, ec.cep AS cepCob, ec.complemento AS cplCob, ec.bairro AS baiCob, ec.cidade AS munCob, ec.uf AS ufCob, ec.codMunicipio AS codUfCob,
          c.email, c.codVendedor, c.dataCadastro
        FROM clientes c
        LEFT OUTER JOIN enderecos e ON c.idEndereco = e.idEndereco
        LEFT OUTER JOIN enderecos ec ON c.idEnderecoCobranca = ec.idEndereco
      ";

    $where = [];
    $params = [];

    if (!empty($cpfCnpj)) {
      $where[] = "c.cpfCnpj = :cpfCnpj";
      $params[':cpfCnpj'] = $cpfCnpj;
    }
    if (!empty($nomeCli)) {
      $where[] = "c.razaoSocial LIKE :nomeCli";
      $params[':nomeCli'] = '%' . $nomeCli . '%';
    }

    if ($where) {
      $sql .= "\n WHERE " . implode(' AND ', $where);
    } else {
      $sql .= "\n WHERE c.dataCadastro > '20220901' ";
    }

    $sql .= "\n ORDER BY razaoSocial, dataCadastro DESC";
    $stmt = $this->capt->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  function consultaClientesGestor($dados): array
  {
    $nomeCli = $dados['nomeCli'] ?? '';
    $cpfCnpj = $dados['cpfCnpj'] ?? '';

    $sql =
      "SELECT 
        c.codigoDaPessoa, c.nomeRazaoSocial, c.nomeFantasia, c.identMF, c.numeroDoRg,
        c.telefone, c.celular,
        e.siglaTipoLogradouro, e.nomeDoLogradouro, e.numeroDoEndereco, e.cep, e.complementoDoEndereco, e.nomeDoBairro, e.nomeDoMunicipio, e.siglaDaUf,
        c.email, c.dataDeCadastro
        FROM vCadPessoaFisicaJuridica c
        INNER JOIN vCadEnderecoCompleto e WITH (NOLOCK) ON c.codigoDaPessoa = e.codigoDaPessoa
      ";

    $where = [];
    $params = [];

    if (!empty($cpfCnpj)) {
      $where[] = "c.identMF = :cpfCnpj";
      $params[':cpfCnpj'] = $cpfCnpj;
    }
    if (!empty($nomeCli)) {
      $where[] = "c.nomeRazaoSocial LIKE :nomeCli";
      $params[':nomeCli'] = '%' . $nomeCli . '%';
    }

    if ($where) {
      $sql .= "\n WHERE " . implode(' AND ', $where);
    } else {
      $sql .= "\n WHERE c.dataDeCadastro > '20250101' ";
    }

    $sql .= "\n ORDER BY nomeRazaoSocial, dataDeCadastro DESC";
    $stmt = $this->gestor->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  function consultaClientesSenior(): array
  {
    $sql =
      "SELECT top 10 c.codcli, c.nomcli, c.apecli, c.tipcli, c.tipmer, c.insest, c.cgccpf, 
          c.foncli, c.foncl2, c.foncl3, c.foncl4, c.foncl5, c.faxcli,
          c.endcli, c.nencli, c.cplend, c.cepcli, c.cepini, c.baicli, c.cidcli, c.sigufs, c.codpai,
          c.usucad, c.datcad
        FROM e085cli c
      ";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  function integrarCliente($dados): array
  {
    $wsdl = 'http://10.64.0.89:8080/g5-senior-services/sapiens_Synccom_senior_g5_co_ger_cad_clientes?wsdl';
    $client = new SoapClient($wsdl, [
      'trace' => true,
      'exceptions' => true,
      'cache_wsdl' => WSDL_CACHE_NONE
    ]);

    $user = 'cadena';
    $password = 'cadena';
    $encryption = 0;

    // Transforma a string JSON em array de clientes
    $clientes = json_decode('[' . $dados['selected_ids'] . ']', true);
    $resultados = [];

    foreach ($clientes as $cliente) {
      // Mapeia os dados
      $cpfCnpj = trim($cliente['cpfCnpj']) ?? '';
      $tipoCli = strlen($cpfCnpj) > 11 ? 'J' : 'F';
      $tipEmp = '1';
      $tipMer = 'I';

      $codEmp          = '1';
      $codFil          = '1';
      $identSistemas   = 'capt';
      $codCliente      = $cliente['codCliente'] ?? '';
      $razaoSocial     = $cliente['razaoSocial'] ?? '';
      $nomeFantasia    = $cliente['nomeFantasia'] ?? '';
      $inscrEstadual   = $cliente['inscrEstadual'] ?? '';
      $telefoneFixoDDD = $cliente['telefoneFixoDDD'] ?? '';
      $telefoneFixoNro = $cliente['telefoneFixoNro'] ?? '';
      $celularDDD      = $cliente['celularDDD'] ?? '';
      $celularNro      = $cliente['celularNro'] ?? '';
      $email           = $cliente['email'] ?? '';
      $endCli          = $cliente['logradouro'] ?? '';
      $numEnd          = $cliente['nro'] ?? '';
      $baiCli          = $cliente['bairro'] ?? '';
      $cplCli          = $cliente['complemento'] ?? '';
      $munCli          = $cliente['cidade'] ?? '';
      $ufCli           = $cliente['uf'] ?? '';
      $cepCli          = $cliente['cep'] ?? '';
      $endCob          = $cliente['endcob'] . $cliente['nroCob'] ?? '';
      $baiCob          = $cliente['baiCob'] ?? '';
      $cplCob          = $cliente['cplCob'] ?? '';
      $munCob          = $cliente['munCob'] ?? '';
      $ufCob           = $cliente['ufCob'] ?? '';
      $cepCob          = $cliente['cepCob'] ?? '';
      $codVendedor     = $cliente['codVendedor'] ?? '';
      $dataCadastro    = $cliente['dataCadastro'] ?? '';
      // Aqui você pode usar as variáveis conforme necessário

      // Monta o struct IntegracaoCliente
      $dadosGerais = [
        'codCli'   => (int) $codCliente,
        'nomCli'   => $razaoSocial,
        'apeCli'   => strval($nomeFantasia),
        'tipCli'   => $tipoCli,
        'tipEmp'   => $tipEmp,
        'tipMer'   => $tipMer,
        'insEst'   => $inscrEstadual,
        'cgcCpf'   => preg_replace('/[^0-9]/', '', $cpfCnpj),
        'endCli'   => $endCli,
        'nenCli'   => $numEnd,
        'cplEnd'   => $cplCli,
        'baiCli'   => $baiCli,
        'cidCli'   => $munCli,
        'sigUfs'   => $ufCli,
        'cepCli'   => preg_replace('/[^0-9]/', '', $cepCli),
        'fonCli'   => $telefoneFixoDDD . $telefoneFixoNro,
        'fonCl2'   => $celularDDD . $celularNro,
        'emaNfe'   => $email,
        'intNet'   => $email
      ];

      // Monta os parâmetros para GravarClientes
      $params = [
        'dadosGeraisCliente' => $dadosGerais,
        'dataBuild' => date('Ymd'),
        'flowInstanceID' => '',
        'flowName' => '',
        'idtReq' => uniqid('req'),
        'sigInt' => 'capt'
      ];
      try {
        $resposta = $client->GravarClientes($user, $password, $encryption, ['parameters' => $params]);
        $resultados[] = [
          'cliente' => $cpfCnpj,
          'status' => 'Sucesso',
          'resposta' => $resposta
        ];
      } catch (SoapFault $e) {
        $resultados[] = [
          'cliente' => $cpfCnpj,
          'status' => 'Erro',
          'mensagem' => $e->getMessage()
        ];
      }
    }
    return $resultados;
  }
}
