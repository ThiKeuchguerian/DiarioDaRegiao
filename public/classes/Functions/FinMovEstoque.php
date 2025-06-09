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
   * @param string|null    $codDep
   * @param string|null $dtInicio  formato 'YYYY-MM-DD' ou 'DD/MM/YYYY'
   * @param string|null $dtFim
   * @return array
   */
  public function mediaHistoricoMovimento(
    ?string $codDep   = null,
    ?string $dtInicio = null,
    ?string $dtFim    = null
  ): array {
    $sql =
      " SELECT M.CODPRO AS CodPro, P.DESPRO   AS DescPro, AVG(M.QTDMOV) AS QtdeMov, AVG(M.VLRMOV) AS VlrMov, AVG(M.PRMEST) AS PrecoMedio
          FROM E210MVP M
        INNER JOIN E070EST E ON ((E.CODEMP = M.CODEMP) AND (E.CODFIL = M.FILDEP))
        INNER JOIN E075PRO P ON ((P.CODEMP=M.CODEMP) AND (P.CODPRO=M.CODPRO))
        LEFT JOIN E000MVI MI ON ((MI.CODEMP=M.CODEMP)  AND (MI.CODPRO = M.CODPRO) AND (MI.CODDER = M.CODDER) AND (MI.CODDEP = M.CODDEP) AND (MI.DATMOV = M.DATMOV)   AND (MI.SEQMOV = M.SEQMOV))
        INNER JOIN E075DER D ON ((D.CODEMP=M.CODEMP) AND (D.CODPRO=M.CODPRO) AND (D.CODDER=M.CODDER)) 
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
      $d1 = (new DateTime($dtInicio))->format('Ymd');
      $d2 = (new DateTime($dtFim))->format('Ymd');
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
  public function mediaAnaliticoMovimento(?string $codDep, string $dtInicio, string $dtFim): array
  {
    $sql =
      "SELECT M.CODPRO AS CodPro, P.DESPRO AS DesPro, M.DATMOV AS DtMov,
          AVG(M.QTDMOV) AS QtdeMov,
          AVG(M.VLRMOV) AS VlrMov,
          AVG(M.PRMEST) AS PrecoMedio,
          CASE
            WHEN 
              CAST(
              CAST((ROUND(AVG(M.PRMEST), 2, 3)) AS DECIMAL(10, 2)) - 
              LAG(CAST((ROUND(AVG(M.PRMEST), 2, 3)) AS DECIMAL(10, 2))) OVER (
                PARTITION BY M.CODPRO 
                ORDER BY M.DATMOV
              ) AS DECIMAL(10, 2)
              ) IS NULL THEN '0.00'
            ELSE CAST(
              CAST((ROUND(AVG(M.PRMEST), 2, 3)) AS DECIMAL(10, 2)) - 
              LAG(CAST((ROUND(AVG(M.PRMEST), 2, 3)) AS DECIMAL(10, 2))) OVER (
                PARTITION BY M.CODPRO 
                ORDER BY M.DATMOV
              ) AS DECIMAL(10, 2)
            )
          END AS DiferencaPreco
        FROM E210MVP M
        INNER JOIN E070EST E ON ((E.CODEMP = M.CODEMP) AND (E.CODFIL = M.FILDEP))   
        INNER JOIN E075PRO P ON ((P.CODEMP=M.CODEMP) AND (P.CODPRO=M.CODPRO))   
        LEFT JOIN E000MVI MI ON ((MI.CODEMP=M.CODEMP)  AND (MI.CODPRO = M.CODPRO) AND (MI.CODDER = M.CODDER) AND (MI.CODDEP = M.CODDEP) AND (MI.DATMOV = M.DATMOV)   AND (MI.SEQMOV = M.SEQMOV))
        INNER JOIN E075DER D ON ((D.CODEMP=M.CODEMP) AND (D.CODPRO=M.CODPRO) AND (D.CODDER=M.CODDER)) 
        WHERE M.CODEMP = 1 AND M.CODDEP  = :codDep AND M.ESTMOV IN('NO','NB','NR')
      ";

    $params = [':codDep' => $codDep];
    if ($dtInicio <> '' && $dtFim <> '') {
      $d1 = (new DateTime($dtInicio))->format('Ymd');
      $d2 = (new DateTime($dtFim))->format('Ymd');
      $sql .= "\n AND M.DATMOV BETWEEN :d1 AND :d2";
      $params[':d1'] = $d1;
      $params[':d2'] = $d2;
    }

    $sql .= "\n  GROUP BY M.CODPRO, P.DESPRO, M.DATMOV ORDER BY M.CODPRO, M.DATMOV";
    
    $stmt = $this->senior->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Detalhe completo das movimentações de um item
   */
  public function detalheMovimentoItem(
    string  $codDep,
    string  $codPro,
    ?string $dtInicio = null,
    ?string $dtFim    = null
  ): array {

    $sql =
      " SELECT
          M.coddep AS Deposito,
          M.numdoc AS NumDoc,
          M.codpro,
          P.despro AS DescrFis,
          P.codfam AS Familia,
          M.codtns AS Transacao,
          M.seqmov AS Seq,
          P.unimed AS UM,
          M.esteos AS Tipo,
          U.r910usu_nomcom AS Operador,
          M.qtdest AS QtdeEst,
          M.vlrmov AS VlrMov,
          M.vlrest AS VlrEst,
          M.qtdmov AS QtdeMovi,
          M.PRMEST AS PreMed,
          M.datdig AS DtDigitada,
          M.datmov AS DtMovimento
        FROM E210MVP M
        INNER JOIN E070EST E ON E.CODEMP = M.CODEMP AND E.CODFIL = M.FILDEP  
        INNER JOIN E075PRO P ON P.CODEMP = M.CODEMP AND P.CODPRO = M.CODPRO  
        LEFT JOIN E000MVI MI ON MI.CODEMP = M.CODEMP  AND MI.CODPRO = M.CODPRO AND MI.CODDER = M.CODDER AND MI.CODDEP = M.CODDEP AND MI.DATMOV = M.DATMOV AND MI.SEQMOV = M.SEQMOV
        INNER JOIN E075DER D ON D.CODEMP = M.CODEMP AND D.CODPRO = M.CODPRO AND D.CODDER = M.CODDER
        INNER JOIN EW99USU U ON M.usurec = U.r999usu_codusu
        WHERE M.CODEMP = 1
          AND M.coddep = :codDep
          AND M.codpro = :codPro
      ";

    $params = [
      ':codDep' => $codDep,
      ':codPro' => $codPro
    ];
    if ($dtInicio && $dtFim) {
      $d1 = (new DateTime($dtInicio))->format('Ymd');
      $d2 = (new DateTime($dtFim))->format('Ymd');
      $sql .= "\n  AND M.datmov BETWEEN :d1 AND :d2";
      $params[':d1'] = $d1;
      $params[':d2'] = $d2;
    }

    $sql .= "\n ORDER BY M.codpro, M.datmov";
    // depurar($sql, $params);
    $stmt = $this->senior->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
