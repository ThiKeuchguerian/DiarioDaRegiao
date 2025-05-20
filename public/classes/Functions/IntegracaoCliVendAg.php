<?php
require_once __DIR__ . '/../DBConnect.php';

class IntegracaoCliVendAg
{
  private $senior;
  private $capt;
  private $tecmidia;

  public function __construct()
  {
    $this->senior   = DatabaseConnection::getConnection('senior');
    $this->capt     = DatabaseConnection::getConnection('capt');
    $this->tecmidia = DatabaseConnection::getConnection('tecmidia');

    $this->senior->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
    $this->capt->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
    $this->tecmidia->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
  }

  /**
   * Método genérico que prepara, faz bind de todos os params e retorna o fetchAll()
   */
  private function runQuery(\PDO $conn, string $sql, array $params = []): array
  {
    $stmt = $conn->prepare($sql);

    foreach ($params as $ph => $val) {
      $stmt->bindValue($ph, $val, \PDO::PARAM_STR);
    }
    // echo "<pre>";
    // var_dump($val);
    // die();
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }
  /**
   * Prepara, faz bindValue e executa um SQL de atualização
   * Retorna o número de linhas afetadas (rowCount).
   */
  private function runExec(\PDO $conn, string $sql, array $params = []): int
  {
    $stmt = $conn->prepare($sql);
    foreach ($params as $ph => $val) {
      $stmt->bindValue($ph, $val, \PDO::PARAM_STR);
    }
    // echo "<pre>";
    // var_dump($stmt);
    // var_dump($params);
    // die();
    $stmt->execute();
    return $stmt->rowCount();
  }
  function ClienteCapt(string $cpfCnpj): array
  {
    $sql = "SELECT Sistema='Capt', c.idCliente AS ID, c.codCliente AS CodCliente, c.razaoSocial AS NomeCliente, c.cpfCnpj AS CpfCnpj, c.codVendedor AS CodVendedor,
      CASE
        WHEN LEN(c.cpfCnpj) = 11 THEN 'Física'
        WHEN LEN(c.cpfCnpj) = 14 THEN 'Jurídica'
      END AS Tipo FROM clientes c 
      WHERE c.cpfCnpj LIKE :cpfCnpj 
    ";

    // echo "<pre>";
    // var_dump($sql);
    // die();

    return $this->runQuery($this->capt, $sql, [
      ':cpfCnpj' => $cpfCnpj . '%'
    ]);
  }

  function ClienteSenior(string $cpfCnpj): array
  {
    $sql = "SELECT Sistema='Sapiens', c.codcli AS ID, c.idecli AS CodCliente, c.nomcli AS NomeCliente, c.cgccpf AS CpfCnpj, c.apecli, 
      CASE 
        WHEN c.tipcli = 'F' THEN 'Física'
        WHEN c.tipcli = 'J' THEN 'Jurídica'
      END AS Tipo
      FROM e085cli c
      WHERE c.cgccpf LIKE :cpfCnpj
    ";

    // echo "<pre>";
    // var_dump($sql);
    // die();

    return $this->runQuery($this->senior, $sql, [
      ':cpfCnpj' => $cpfCnpj . '%'
    ]);
  }

  function ClienteSeniorInt(string $cpfCnpj): array
  {
    $sql = "SELECT Sistema='SapiensIntegracao', intc.usu_codcli AS ID, intc.usu_zr_cod AS CodCliente, intc.usu_zr_codvend AS CodVendedor, intc.usu_zr_desc AS NomeCliente, intc.usu_zr_cgc AS CpfCnpj,
      CASE
        WHEN LEN(intc.usu_zr_cgc) = 11 THEN 'Física'
        WHEN LEN(intc.usu_zr_cgc) = 14 THEN 'Jurídica'
      END AS Tipo FROM usu_tszr010 intc
      WHERE intc.usu_zr_dtgera = (SELECT MAX(intc.usu_zr_dtgera) FROM usu_tszr010 intc WHERE intc.usu_zr_cgc = :cpfCnpj )
        AND  intc.usu_zr_cgc LIKE :cpfCnpj
    ";

    // echo "<pre>";
    // var_dump($sql, $cpfCnpj);
    // die();

    return $this->runQuery($this->senior, $sql, [
      ':cpfCnpj' => $cpfCnpj . '%'
    ]);
  }

  function ClienteOrcamentoGrafica(string $cpfCnpj): array
  {
    $sql = "SELECT	Sistema = 'Grafica', codcli AS ID, idecli AS CodCliente, CAST(cgccpf AS VARCHAR) CpfCnpj, nomcli NomeCliente, apecli NOME_FANTASIA,
      CASE
        WHEN LEN(cgccpf) = 11 THEN 'Física'
        WHEN LEN(cgccpf) = 14 THEN 'Jurídica'
      END AS Tipo
      FROM	e085cli WITH (NOLOCK)
        WHERE	sitcli = 'A' AND ISNUMERIC(idecli) = 1 and cgccpf LIKE :cpfCnpj 
      ORDER	BY nomcli, apecli 
    ";

    // echo "<pre>";
    // var_dump($sql, $cpfCnpj);
    // die();

    return $this->runQuery($this->senior, $sql, [
      ':cpfCnpj' => $cpfCnpj . '%'
    ]);
  }

  function ClienteEasyClass(string $cpfCnpj): array
  {
    $sql = "SELECT Sistema='EasyClass', c.customer_id AS ID, c.customer_id AS CodCliente, c.fullname AS NomeCliente, c.id_value AS CpfCnpj, c.id_type,
      CASE 
        WHEN c.id_type = '0' THEN 'Física'
        WHEN c.id_type = '1' THEN 'Jurídica'
      END AS Tipo FROM ec_customer c
      WHERE c.id_value LIKE :cpfCnpj 
    ";

    return $this->runQuery($this->tecmidia, $sql, [
      ':cpfCnpj' => $cpfCnpj . '%'
    ]);
  }

  function VendedorCapt(string $nome): array
  {
    $sql = "SELECT v.idVendedor, v.codVendedor, '' AS Equipe, v.nome, v.situacao, v.cpf 
      FROM vendedores v
      WHERE v.nome LIKE :NomeVend /*AND v.situacao = 'A'*/";

    return $this->runQuery($this->capt, $sql, [
      ':NomeVend' => $nome . '%'
    ]);
  }

  function VendedorSenior(string $nome): array
  {
    $sql = " SELECT v.codrep, v.usu_iderep, v.codcdi, v.nomrep, v.sitrep, v.cgccpf 
      FROM e090rep v
      WHERE v.nomrep LIKE :NomeVend AND v.sitrep = 'A' ORDER BY v.nomrep 
    ";

    return $this->runQuery($this->senior, $sql, [
      ':NomeVend' => $nome . '%'
    ]);
  }

  function AgenciaCapt(string $cpfCnpj): array
  {
    $sql = " SELECT a.codAgencia, a.nome, a.cnpj, a.situacao, a.tipo, a.dataCadastro, a.idUsuCadastro, a.dataAlteracao, a.idUsuAlteracao
      FROM agencias a WHERE a.cnpj LIKE :CNPJAgen 
    ";
    return $this->runQuery($this->capt, $sql, [
      ':cpfCnpj' => $cpfCnpj . '%'
    ]);
  }

  function AgenciaSenior(string $cpfCnpj): array
  {
    $sql = " SELECT a.usu_iderep, a.nomrep, a.cgccpf, a.tiprep, a.sitrep
      FROM e090rep a
      WHERE a.cgccpf LIKE :CNPJAgen 
    ";

    return $this->runQuery($this->senior, $sql, [
      ':cpfCnpj' => $cpfCnpj . '%'
    ]);
  }

  public function updateCliente(
    string $sistema,
    string $nomeCliente,
    string $cpfCnpj,
    string $codCliente,
    ?string $tipoCliente = null,
    ?string $codVendedor = null
    ): int {
    switch ($sistema) {
      case 'Capt':
        $conn   = $this->capt;
        $sql    = "
          UPDATE clientes
             SET codCliente  = :codCliente,
                 razaoSocial = :nomeCliente,
                 codVendedor = :codVendedor
           WHERE cpfCnpj = :cpfCnpj
        ";
        $params = [
          ':codCliente'  => $codCliente,
          ':nomeCliente' => $nomeCliente,
          ':codVendedor' => $codVendedor,
          ':cpfCnpj'     => $cpfCnpj,
        ];
        break;

      case 'Sapiens':
        $conn   = $this->senior;
        $sql    = "
          UPDATE e085cli
             SET idecli  = :codCliente,
                 nomcli  = :nomeCliente
           WHERE cgccpf  = :cpfCnpj
        ";
        $params = [
          ':codCliente'  => $codCliente,
          ':nomeCliente' => $nomeCliente,
          ':cpfCnpj'     => $cpfCnpj,
        ];
        break;

      case 'SapiensIntegracao':
        $conn   = $this->senior;
        $sql    = "
          UPDATE usu_tszr010
             SET usu_zr_cod     = :codCliente,
                 usu_zr_desc    = :nomeCliente,
                 usu_zr_codvend = :codVendedor
           WHERE usu_zr_cgc     = :cpfCnpj
        ";
        $params = [
          ':codCliente'  => $codCliente,
          ':nomeCliente' => $nomeCliente,
          ':codVendedor' => $codVendedor,
          ':cpfCnpj'     => $cpfCnpj,
        ];
        break;

      case 'EasyClass':
        $conn   = $this->tecmidia;
        // escolhe o id_type de acordo com $tipoCliente
        $novoTipo = ($tipoCliente === '1') ? '0' : '1';
        $sql    = "
          UPDATE ec_customer
             SET id_type = {$conn->quote($novoTipo)}
           WHERE id_value    = :cpfCnpj
             AND customer_id = :codCliente
        ";
        $params = [
          ':cpfCnpj'    => $cpfCnpj,
          ':codCliente' => $codCliente,
        ];
        break;

      default:
        throw new InvalidArgumentException("Sistema “{$sistema}” não reconhecido.");
    }

    return $this->runExec($conn, $sql, $params);
  }
}
