<?php
require_once __DIR__ . '/../DBConnect.php';

class ComercialCheckMetas
{
  // Conexões
  private $capt;

  public function __construct()
  {
    $this->capt = DatabaseConnection::getConnection('capt');
  }

  /**
   * @param array $meses     Ex: ['05/2025','06/2025','07/2025','08/2025']
   * @return array
   */

  public string $queryPadrao = "SELECT
      Con.nroContrato, DCon.dataVeiculacao,
      FORMAT(DCon.dataVeiculacao, 'MM/yyyy') AS MesAno,
      Pro.nomeGrupo, Pro.nomeSecao, Con.tituloAnuncio, Con.tipoContrato, Cli.nomeFantasia, Ven.nomeReduzido, 
    (Con.valor / COUNT(DCon.dataVeiculacao) OVER (PARTITION BY Con.nroContrato)) AS ValorVeiculado
    FROM Contratos Con
      INNER JOIN Contratos_Datas DCon WITH (NOLOCK) ON Con.nroContrato = DCon.nroContrato
      INNER JOIN Produtos Pro WITH (NOLOCK) ON Con.codProduto = Pro.codProduto
      INNER JOIN Clientes Cli WITH (NOLOCK) ON Con.idCliente = Cli.idCliente
      INNER JOIN vendedores Ven WITH (NOLOCK) ON Con.codVendedor = Ven.codVendedor
  ";

  public function ConsultaCheckMetasGrupo(string $Ano): array
  {
    if ($Ano != '') {
      $query = $this->queryPadrao . "\n WHERE DCon.dataVeiculacao BETWEEN '{$Ano}0101' AND '{$Ano}1231' AND Con.tipoContrato <> '2' ";
      $params[] = $Ano;
    }

    // Ordenando
    $OrderBy = "\n ORDER BY nomeGrupo";

    // Monta query
    $sql = "WITH CheckMetas AS ( "
      . $query
      . "), Ajustando AS (
          SELECT nomeGrupo, MesAno, SUM(ValorVeiculado) AS ValorVeiculado
            FROM CheckMetas
            GROUP BY nomeGrupo, MesAno
        )
          SELECT
            nomeGrupo,
            [01/{$Ano}], [02/{$Ano}], [03/{$Ano}], [04/{$Ano}], [05/{$Ano}], [06/{$Ano}], 
            [07/{$Ano}], [08/{$Ano}], [09/{$Ano}], [10/{$Ano}], [11/{$Ano}], [12/{$Ano}] FROM Ajustando
          PIVOT (
            SUM(ValorVeiculado)
            FOR MesAno IN (
              [01/{$Ano}], [02/{$Ano}], [03/{$Ano}], [04/{$Ano}], [05/{$Ano}], [06/{$Ano}], 
              [07/{$Ano}], [08/{$Ano}], [09/{$Ano}], [10/{$Ano}], [11/{$Ano}], [12/{$Ano}]
            )
        ) AS P "
      . $OrderBy
    ;

    // echo "<pre>";
    // var_dump($sql);
    // die();

    // Prepara e executa
    $stmt = $this->capt->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function ConsultaCheckMetasProduto(string $Ano): array
  {
    if ($Ano != '') {
      $query = $this->queryPadrao . "\n WHERE DCon.dataVeiculacao BETWEEN '{$Ano}0101' AND '{$Ano}1231' AND Con.tipoContrato <> '2' ";
      $params[] = $Ano;
    }

    // Ordenando
    $OrderBy = "\n ORDER BY nomeGrupo";

    // Monta query
    $sql = "WITH CheckMetas AS ( "
      . $query
      . "), Ajustando AS (
          SELECT nomeGrupo, nomeSecao, MesAno, SUM(ValorVeiculado) AS ValorVeiculado
            FROM CheckMetas
            GROUP BY nomeGrupo, nomeSecao, MesAno
        )
          SELECT
            nomeGrupo,nomeSecao,
            [01/{$Ano}], [02/{$Ano}], [03/{$Ano}], [04/{$Ano}], [05/{$Ano}], [06/{$Ano}], 
            [07/{$Ano}], [08/{$Ano}], [09/{$Ano}], [10/{$Ano}], [11/{$Ano}], [12/{$Ano}] FROM Ajustando
          PIVOT (
            SUM(ValorVeiculado)
            FOR MesAno IN (
              [01/{$Ano}], [02/{$Ano}], [03/{$Ano}], [04/{$Ano}], [05/{$Ano}], [06/{$Ano}], 
              [07/{$Ano}], [08/{$Ano}], [09/{$Ano}], [10/{$Ano}], [11/{$Ano}], [12/{$Ano}]
            )
        ) AS P "
      . $OrderBy . ", nomeSecao"
    ;

    // echo "<pre>";
    // var_dump($sql);
    // die();

    // Prepara e executa
    $stmt = $this->capt->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function ConsultaCheckMetasSomaTotal(string $Ano): array
  {
    if ($Ano != '') {
      $query = $this->queryPadrao . "\n WHERE DCon.dataVeiculacao BETWEEN '{$Ano}0101' AND '{$Ano}1231' AND Con.tipoContrato <> '2' ";
      $params[] = $Ano;
    }

    // Ordenando
    $OrderBy = "\n ORDER BY nomeGrupo";

    // Monta query
    $sql = "WITH CheckMetas AS ( "
      . $query
      . "), Ajustando AS (
          SELECT MesAno, SUM(ValorVeiculado) AS ValorVeiculado
            FROM CheckMetas
            GROUP BY MesAno
        )
          SELECT
            [01/{$Ano}], [02/{$Ano}], [03/{$Ano}], [04/{$Ano}], [05/{$Ano}], [06/{$Ano}], 
            [07/{$Ano}], [08/{$Ano}], [09/{$Ano}], [10/{$Ano}], [11/{$Ano}], [12/{$Ano}] FROM Ajustando
          PIVOT (
            SUM(ValorVeiculado)
            FOR MesAno IN (
              [01/{$Ano}], [02/{$Ano}], [03/{$Ano}], [04/{$Ano}], [05/{$Ano}], [06/{$Ano}], 
              [07/{$Ano}], [08/{$Ano}], [09/{$Ano}], [10/{$Ano}], [11/{$Ano}], [12/{$Ano}]
            )
        ) AS P "
    ;

    // echo "<pre>";
    // var_dump($sql);
    // die();

    // Prepara e executa
    $stmt = $this->capt->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
