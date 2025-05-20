<?php
require_once __DIR__ . '/../DBConnect.php';

class ComRelatorioContratoCapt
{
  // Conexões
  private $capt;

  public function __construct()
  {
    $this->capt = DatabaseConnection::getConnection('capt');
  }

  /**
   * @param array $meses     Ex: ['05/2025','06/2025','07/2025','08/2025']
   * @param int   $codProduto 0 (todos), 1, 3, 11 ou 13
   * @return array
   * @throws InvalidArgumentException
   */

  public function ConsultaCliente()
  {
    $query = "SELECT Cli.idCliente, CONCAT(Cli.cpfCnpj, ' - ', Cli.nomeFantasia) AS NomeCli FROM Clientes Cli";

    // Prepara e executa
    $stmt = $this->capt->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function ConsultaGrupo()
  {
    $query = "SELECT DISTINCT(nomeGrupo) FROM Produtos WHERE Situacao = 'A' AND nomeGrupo <> 'NULL' ORDER BY nomeGrupo";

    // Prepara e executa
    $stmt = $this->capt->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function ConsultaProduto($NomeGrupo)
  {
    $query = "SELECT Pro.codProduto, CONCAT(Pro.nomeGrupo, ' + ', Pro.nomeSecao) AS Produto FROM Produtos Pro WHERE Pro.situacao = 'A' AND Pro.nomeGrupo = '$NomeGrupo'";

    // Prepara e executa
    $stmt = $this->capt->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function ConsultaContratoCapt($DtInicial, $DtFinal, $Grupo, $Produto, $Tipo, $Cliente)
  {
    $query = "SELECT Con.nroContrato, CONVERT(VARCHAR, DCon.dataVeiculacao, 103) AS DtVeiculacao, 
        FORMAT(DCon.dataVeiculacao, 'MM,yyyy') AS MesAno,
        CONCAT(Pro.nomeGrupo, ' + ', Pro.nomeSecao) AS Produto, Con.tituloAnuncio,
        CASE
          WHEN Con.tipoContrato = '1' THEN 'Normal'
          WHEN Con.tipoContrato = '2' THEN 'Bonificação'
          WHEN Con.tipoContrato = '3' THEN 'Calhau'
          WHEN Con.tipoContrato = '4' THEN 'Compensação'
          WHEN Con.tipoContrato = '5' THEN 'Anunc. da Casa'
          WHEN Con.tipoContrato = '8' THEN 'Permuta'
          WHEN Con.tipoContrato = '9' THEN 'Cortesia'
          WHEN Con.tipoContrato = '10' THEN 'Só Fatura'
          WHEN Con.tipoContrato = '11' THEN 'Só Veicula'
        END AS tipoContrato, Cli.nomeFantasia, Ven.nomeReduzido, 
        (Con.valor / t.TotalDatas) AS ValorVeiculado
      FROM Contratos Con
      INNER JOIN Contratos_Datas DCon WITH (NOLOCK) ON Con.nroContrato = DCon.nroContrato
      INNER JOIN Produtos Pro WITH (NOLOCK) ON Con.codProduto = Pro.codProduto
      INNER JOIN Clientes Cli WITH (NOLOCK) ON Con.idCliente = Cli.idCliente
      INNER JOIN vendedores Ven WITH (NOLOCK) ON Con.codVendedor = Ven.codVendedor
      CROSS APPLY ( SELECT COUNT(*) AS TotalDatas FROM Contratos_Datas x WHERE x.nroContrato = Con.nroContrato ) t
    ";

    // Sempre verificar se as datas foram informadas
    if ($DtInicial !== '' && $DtFinal !== '') {
      $filtros[] = "DCon.dataVeiculacao BETWEEN '$DtInicial' AND '$DtFinal'";
    }

    if ($Cliente !== '') {
      $filtros[] = "Cli.nomeFantasia LIKE '%$Cliente%'";
    }

    if ($Produto !== '') {
      $filtros[] = "Con.codProduto = '$Produto'";
    }

    if ($Tipo !== '') {
      $filtros[] = "Con.tipoContrato = '$Tipo'";
    }

    if ($Grupo !== '') {
      $filtros[] = "Pro.nomeGrupo = '$Grupo'";
    }

    if (count($filtros) > 0) {
      $sql = $query . " WHERE " . implode(" AND ", $filtros);
    } else {
      $sql = $query; // sem filtros
    }
    
    // Prepara, faz bind e executa
    $stmt = $this->capt->prepare($sql);
    // echo "<pre>";
    // var_dump($query);
    // var_dump($Meses);
    // die();
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
