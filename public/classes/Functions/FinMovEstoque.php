<?php
require_once __DIR__ . '/../DBConnect.php';

class MovimentoEstoque
{
  private $senior;

  public function __construct()
  {
    $this->senior = DatabaseConnection::getConnection('senior');
  }

  /**
   * Lista todos os depósitos (CODEMP = 1)
   * @return array
   */
  public function listarDepositos(): array
  {
    $sql  = "SELECT CODDEP, DESDEP FROM e205dep WHERE CODEMP = 1 ORDER BY CODDEP";
    $stmt = $this->senior->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Lista produtos de um depósito
   * @param int $codDep
   * @return array
   */
  public function listarProdutosPorDeposito(string $codDep): array
  {
    $sql  = 
      "SELECT P.CODPRO, P.DESPRO FROM E075PRO P
        INNER JOIN e210est M WITH(NOLOCK) ON P.CODEMP = M.CODEMP AND P.CODPRO  = M.CODPRO
        WHERE M.CODDEP = :codDep
        ORDER BY P.CODPRO
      ";
    $stmt = $this->senior->prepare($sql);
    $stmt->execute([':codDep' => $codDep]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Média histórica de movimentação
   *
   * @param int|null    $codDep
   * @param string|null $dtInicio  formato 'YYYY-MM-DD' ou 'DD/MM/YYYY'
   * @param string|null $dtFim
   * @return array
   */
  public function mediaHistoricoMovimento(
    ?int    $codDep   = null,
    ?string $dtInicio = null,
    ?string $dtFim    = null
  ): array {
    $sql = 
      " SELECT M.CODPRO   AS CodPro, P.DESPRO   AS DescPro, AVG(M.QTDMOV) AS QtdeMov, AVG(M.VLRMOV) AS VlrMov, AVG(M.PRMEST) AS PrecoMedio
          FROM E210MVP M
        INNER JOIN E075PRO P ON P.CODEMP = M.CODEMP AND P.CODPRO = M.CODPRO
        WHERE M.CODEMP = 1 AND M.ESTMOV IN ('NO','NB','NR')
      ";

    $params = [];
    $conds  = [];

    if ($codDep !== null) {
      $conds[]           = "M.CODDEP = :codDep";
      $params[':codDep'] = $codDep;
    }

    if ($dtInicio && $dtFim) {
      // normalize date para YYYY-MM-DD
      $d1 = (new DateTime($dtInicio))->format('Y-m-d');
      $d2 = (new DateTime($dtFim))->format('Y-m-d');
      $conds[]              = "M.DATMOV BETWEEN :d1 AND :d2";
      $params[':d1']        = $d1;
      $params[':d2']        = $d2;
    }

    if (count($conds)) {
      $sql .= "\n  AND " . implode("\n  AND ", $conds);
    }

    $sql .= "\n  GROUP BY M.CODPRO, P.DESPRO ORDER BY M.CODPRO";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Média analítica de movimentação (mês a mês e diferença)
   */
  public function mediaAnaliticoMovimento(
    int     $codDep,
    ?string $dtInicio = null,
    ?string $dtFim    = null
  ): array {
    $sql = "
          WITH Mov AS (
            SELECT
              M.CODPRO, P.DESPRO, M.DATMOV,
              AVG(M.QTDMOV) OVER (PARTITION BY M.CODPRO,M.DATMOV)    AS QtdeMov,
              AVG(M.VLRMOV) OVER (PARTITION BY M.CODPRO,M.DATMOV)    AS VlrMov,
              AVG(M.PRMEST) OVER (PARTITION BY M.CODPRO,M.DATMOV)   AS PrecoMedio
            FROM E210MVP M
            JOIN E075PRO P
              ON P.CODEMP = M.CODEMP AND P.CODPRO = M.CODPRO
            WHERE M.CODEMP = 1
              AND M.CODDEP  = :codDep
              AND M.ESTMOV IN('NO','NB','NR')
        ";

    $params = [':codDep' => $codDep];
    if ($dtInicio && $dtFim) {
      $d1 = (new DateTime($dtInicio))->format('Y-m-d');
      $d2 = (new DateTime($dtFim))->format('Y-m-d');
      $sql .= "\n    AND M.DATMOV BETWEEN :d1 AND :d2";
      $params[':d1'] = $d1;
      $params[':d2'] = $d2;
    }

    $sql .= "
          ),
          Analit AS (
            SELECT
              CODPRO, DESPRO, DATMOV,
              QtdeMov, VlrMov, PrecoMedio,
              PrecoMedio
               - LAG(PrecoMedio) OVER (PARTITION BY CODPRO ORDER BY DATMOV)
              AS Diferenca
            FROM Mov
          )
          SELECT *
            FROM Analit
           ORDER BY CODPRO, DATMOV
        ";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Detalhe completo das movimentações de um item
   */
  public function detalheMovimentoItem(
    int     $codDep,
    string  $codPro,
    ?string $dtInicio = null,
    ?string $dtFim    = null
  ): array {
    $sql = "
          SELECT
            M.coddep      AS Deposito,
            M.numdoc      AS NumDoc,
            M.codpro,
            P.despro      AS DescrFis,
            P.codfam      AS Familia,
            M.codtns      AS Transacao,
            M.seqmov     AS Seq,
            P.unimed      AS UM,
            M.esteos      AS Tipo,
            U.r910usu_nomcom AS Operador,
            CAST(M.qtdest AS DECIMAL(10,2)) AS QtdeEst,
            CAST(M.vlrmov AS DECIMAL(10,2)) AS VlrMov,
            CAST(M.vlrest AS DECIMAL(10,2)) AS VlrEst,
            CAST(M.qtdmov AS DECIMAL(10,2)) AS QtdeMovi,
            CAST(M.PRMEST AS DECIMAL(10,2)) AS PreMed,
            CONVERT(VARCHAR, M.datdig,103)  AS DtDigitada,
            CONVERT(VARCHAR, M.datmov,103)  AS DtMovimento
          FROM E210MVP M
          JOIN E075PRO P
            ON P.CODEMP = M.CODEMP AND P.CODPRO = M.CODPRO
          JOIN EW99USU U
            ON U.CODEMP = M.CODEMP AND U.r999usu_codusu = M.usurec
          WHERE M.CODEMP = 1
            AND M.coddep = :codDep
            AND M.codpro = :codPro
        ";

    $params = [
      ':codDep' => $codDep,
      ':codPro' => $codPro
    ];
    if ($dtInicio && $dtFim) {
      $d1 = (new DateTime($dtInicio))->format('Y-m-d');
      $d2 = (new DateTime($dtFim))->format('Y-m-d');
      $sql .= "\n  AND M.datmov BETWEEN :d1 AND :d2";
      $params[':d1'] = $d1;
      $params[':d2'] = $d2;
    }

    $sql .= "\nORDER BY M.codpro, M.datmov";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
