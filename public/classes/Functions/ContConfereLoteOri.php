<?php
require_once __DIR__ . '/../DBConnect.php';

class LoteContabil
{
  private $senior;

  public function __construct()
  {
    $this->senior = DatabaseConnection::getConnection('senior');

    $this->senior->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
  }

  public function consultaOrigem()
  {
    $sql = "SELECT DISTINCT ORILCT FROM E640LOT ORDER BY ORILCT";
    $stmt = $this->senior->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  /**
   * Extrai primeiro e último dia no formato YYYYMMDD a partir de 'MM/YYYY'
   */
  public static function obterPrimeiroUltimoDia(string $mesAno): array
  {
    $data = \DateTime::createFromFormat('m/Y', $mesAno);
    if (! $data) {
      throw new \InvalidArgumentException("Mês/Ano inválido: {$mesAno}");
    }
    $primeiro = $data->format('Ym01');               // YYYY-mm-dd
    $ultimo    = $data->modify('last day of this month')->format('Ymd');
    // echo "<pre>";
    // var_dump($primeiro, $ultimo);
    // die();
    return [$primeiro, $ultimo];
  }

  /**
   * Consulta todos os lotes contabil no período + empresa + origem
   * @return array cada linha com os dados de E640LOT
   */

  public function consultaLoteContabil(
    int    $codEmpresa,
    string $mesAno,
    string $origem
  ): array {

    // Obtendo primeiro e ultimo dia do mes
    list($primeiroDia, $ultimoDia) = self::obterPrimeiroUltimoDia($mesAno);

    $sql = "SELECT L.CODEMP, L.NUMLOT, L.TIPLCT, L.ORILCT, L.CODFIL, L.DATLOT, L.DATFIX, L.DESLOT, L.TOTDEB, L.TOTCRE,
        L.TOTINF, L.TOTLCT, L.USULOT, L.CODUSU, L.DATENT,L.HORENT, L.LOTSIN,
        CASE WHEN L.SITLOT = 2 THEN 'Contabilizado' ELSE 'Não Contabilizado' END AS SITLOT,
        (
          SELECT SUM(VLRLCT)
            FROM E640LCT
            WHERE CTADEB <> 0
              AND SITLCT  IN(1,2)
              AND CODEMP  = L.CODEMP
              AND NUMLOT  = L.NUMLOT
        ) AS TOTDEBLCT,
        (
          SELECT SUM(VLRLCT)
            FROM E640LCT
            WHERE CTACRE <> 0
              AND SITLCT  IN(1,2)
              AND CODEMP  = L.CODEMP
              AND NUMLOT  = L.NUMLOT
        ) AS TOTCRELCT
      FROM E640LOT L
      WHERE L.CODEMP   = :codEmp
        AND L.DATLOT  BETWEEN :pd AND :ld
        AND L.SITLOT IN(1,2)
        AND L.ORILCT  = :origem
      ORDER BY L.DATLOT
    ";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute([
      ':codEmp' => $codEmpresa,
      ':pd'     => $primeiroDia,
      ':ld'     => $ultimoDia,
      ':origem' => $origem
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Consulta lançamentos de todos os lotes passados
   * @param int[] $numLots array de números de lote
   * @return array
   */
  public function consultaLancamentoLoteContabil(array $numLots): array
  {
    if (empty($numLots)) {
      return [];
    }
    // gerar placeholders dinâmicos :n0, :n1, …
    $placeholders = [];
    $params       = [];
    foreach ($numLots as $i => $num) {
      $ph                   = ":n{$i}";
      $placeholders[]       = $ph;
      $params[$ph]          = (int)$num;
    }
    $in = implode(', ', $placeholders);

    $sql = "
        SELECT
          C.CODEMP, C.NUMLCT, C.ORILCT, C.CODFIL, C.DATLCT,
          C.CTADEB, C.CTACRE, C.VLRLCT,
          C.CODHPD, C.CPLLCT, C.NUMLOT, C.TEMRAT, C.SITLCT,
          C.OBSCPL, C.CODUSU, C.DATENT, C.HORENT, C.TEMAUX,
          C.CGCCPF, C.CGCCRE, C.NUMFTC, C.DATEXT
        FROM E640LCT C
        WHERE C.CODEMP = 1
          AND C.NUMLOT IN ({$in})
        ORDER BY C.NUMLOT, C.NUMLCT
        ";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
