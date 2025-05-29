<?php
require_once __DIR__ . '/../DBConnect.php';

class CentroCustoOrdermProducao
{
  private $senior;

  public function __construct()
  {
    $this->senior = DatabaseConnection::getConnection('senior');
  }

  /**
   * Média histórica de movimentação
   *
   * @param string|null $mesAno 'MM/YYYY'
   * @return array
   */
  public function movimentoEstoque(
    string $mesAno
  ): array {
    $sql =
      " SELECT M.coddep AS Deposito, M.numdoc AS NumDoc, M.codpro, P.despro AS DescrFis, 
          P.codfam AS Familia, M.codtns AS Transacao, M.seqmov AS Seq, P.unimed AS UM, M.esteos AS Tipo, U.r910usu_nomcom,
          M.qtdest AS QtdeEst, M.vlrmov AS VlrMov, M.vlrest AS VlrEst, M.qtdmov AS QtdeMovi, M.PRMEST AS PreMed, M.datdig AS DtDigitada, M.datmov AS DtMovimento,
        M.ctafin, M.ctared, M.codccu
        FROM E210MVP M
          INNER JOIN E070EST E ON E.CODEMP = M.CODEMP AND E.CODFIL = M.FILDEP  
          INNER JOIN E075PRO P ON P.CODEMP = M.CODEMP AND P.CODPRO = M.CODPRO  
          LEFT JOIN E000MVI MI ON MI.CODEMP = M.CODEMP  AND MI.CODPRO = M.CODPRO AND MI.CODDER = M.CODDER AND MI.CODDEP = M.CODDEP AND MI.DATMOV = M.DATMOV AND MI.SEQMOV = M.SEQMOV
          INNER JOIN E075DER D ON D.CODEMP = M.CODEMP AND D.CODPRO = M.CODPRO AND D.CODDER = M.CODDER
          INNER JOIN EW99USU U ON M.usurec = U.r999usu_codusu
        WHERE M.codemp = 1 AND M.codtns IN (90251, 90201)
      ";

    $params = [];
    $conds  = [];

    if ($mesAno !== null) {
      $conds[] = "FORMAT(M.DATMOV, 'MM/yyyy') = :mesAno";
      $params[':mesAno'] = $mesAno;
    }

    if (count($conds)) {
      $sql .= "\n  AND " . implode("\n  AND ", $conds);
    }

    $sql .= "\n ORDER BY M.datmov, M.numdoc";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Média analítica de movimentação (mês a mês e diferença)
   */
  public function consultaOrdemProducao(string|array $numDoc): array
  {
    $sql =
      "SELECT cop.numorp, cop.codfam, cop.codpro, prd.despro, cop.tmpprv, cop.qtdprv,
          cmo.codcmp, pro.despro, cmo.qtdprv, cmo.qtduti, cmo.codccu FROM e900cop cop
        INNER JOIN e900cmo cmo WITH (NOLOCK) ON cop.codemp = cmo.codemp AND cop.codori = cmo.codori AND cop.numorp = cmo.numorp
        INNER JOIN e075der der WITH (NOLOCK) ON cop.codemp = der.codemp AND cmo.codcmp = der.codpro AND cmo.codder = der.codder
        INNER JOIN e075pro pro WITH (NOLOCK) ON cop.codemp = pro.codemp AND cmo.codcmp = pro.codpro 
        INNER JOIN e075pro prd WITH (NOLOCK) ON cop.codemp = prd.codemp AND cop.codpro = prd.codpro 
        INNER JOIN e093etg etg WITH (NOLOCK) ON cop.codemp = etg.codemp AND cmo.codetg = etg.codetg
      ";

    if (is_array($numDoc)) {
      $placeholders = [];
      $params = [];
      foreach ($numDoc as $idx => $doc) {
        $ph = ":numDoc$idx";
        $placeholders[] = $ph;
        $params[$ph] = $doc;
      }
      $sql .= "\n WHERE cop.numorp IN (" . implode(', ', $placeholders) . ")";
    } else {
      $params = [':numDoc' => $numDoc];
      $sql .= "\n WHERE cop.numorp = :numDoc";
    }

    $sql .= "\n ORDER BY cop.numorp, cmo.codcmp";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
