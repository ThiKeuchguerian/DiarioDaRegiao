<?php
require_once __DIR__ . '/../DBConnect.php';

class ComContratoCadena
{
  // Conexões
  private $cadena;

  public function __construct()
  {
    $this->cadena = DatabaseConnection::getConnection('cadena');
  }

  /**
   * @param string    $NrContrato  001234
   * @return array
   */

  public function ConsultaContrato(string $NrContrato): array
  {
    $query = "SELECT FORMAT(FM.veiculacao, 'MM/yyyy') AS MesVeiculacao, 
        FM.GRUPO_PROGRAMA AS GrupoPrograma, FM.PROGRAMA AS Programa, FM.QUANTIDADE AS Qtde,
        CONVERT(VARCHAR(10), FM.veiculacao, 103) AS DtVeiculacao,
        CAST(FM.VALOR_TABELA AS NUMERIC(9,2)) AS VlrTabela,
        CAST(FM.VALOR_UNITARIO AS NUMERIC(9,2)) AS VlrUnitario,
        CAST(FM.VALOR_TOTAL AS NUMERIC(9,2)) AS VlrTotal
      FROM FM_DIARIO_BI_VENDAS_POR_DIA FM
    ";

    // Monta dinamicamente o where
    $params = [];

    // Valida Filtro
    if ($NrContrato != '') {
      $query .= "\n WHERE FM.contrato_numero = :NumContrato";
      $params[] = $NrContrato;
    }

    // Monta query
    $sql = $query;

    // Verifica a query
    // echo "<pre>";
    // var_dump($sql);
    // die();

    // Prepara e executa
    $stmt = $this->cadena->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function SomaContrato(string $NrContrato): array
  {
    $query = "SELECT MesVeiculacao, CAST(SUM(VALOR_TOTAL) AS NUMERIC(15,2)) AS Valor, 
      COUNT(veiculacao) AS DtVeiculacao, SUM(QUANTIDADE) AS QtdeVeiculacao, VENDEDOR_NOME AS NomeVend
      FROM (
        SELECT FORMAT(veiculacao, 'MM/yyyy') AS MesVeiculacao, VALOR_TOTAL, veiculacao, QUANTIDADE, VENDEDOR_NOME
          FROM FM_DIARIO_BI_VENDAS_POR_DIA
    ";

    // Monta dinamicamente o where
    $params = [];

    // Ordenando
    $OrderBy = ") X GROUP BY MesVeiculacao, VENDEDOR_NOME";

    // Valida Filtro
    if ($NrContrato != '') {
      $query .= "\n WHERE contrato_numero = :NumContrato";
      $params[] = $NrContrato;
    }

    // Monta query
    $sql = $query . $OrderBy;

    // Verifica a query
    // echo "<pre>";
    // var_dump($sql);
    // die();

    // Prepara e executa
    $stmt = $this->cadena->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
