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
    string $mesAno
  ): array {

    // Obtendo primeiro e ultimo dia do mes
    list($primeiroDia, $ultimoDia) = self::obterPrimeiroUltimoDia($mesAno);

    $sql = "SELECT L.CODEMP, L.CODFIL, L.DATLOT, SUM(L.TOTDEB) AS TOTDEB, SUM(L.TOTCRE) AS TOTCRE, SUM(L.TOTINF) AS TOTINF,
        FORMAT(L.DATLOT, 'MM/yyyy') AS MESLOT, (SUM(L.TOTCRE) - SUM(L.TOTDEB)) AS DIF,
        CASE WHEN L.SITLOT = 2 THEN 'Contabilizado' ELSE 'Não Contabilizado' END AS SITLOT
      FROM E640LOT L
      WHERE L.CODEMP = :codEmp
        AND L.DATLOT BETWEEN :pd AND :ld
        AND L.SITLOT = 2
      GROUP BY CODEMP, CODFIL, DATLOT, SITLOT
      ORDER BY L.DATLOT
    ";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute([
      ':codEmp' => $codEmpresa,
      ':pd'     => $primeiroDia,
      ':ld'     => $ultimoDia
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Consulta lançamentos de todos os lotes passados
   * @param int[] $numLots array de números de lote
   * @return array
   */
  public function consultaLancamentoLoteContabil(
    int    $codEmpresa,
    string $mesAno
  ): array {

    // Obtendo primeiro e ultimo dia do mes
    list($primeiroDia, $ultimoDia) = self::obterPrimeiroUltimoDia($mesAno);
    
    $sql = "SELECT
        C.CODEMP, C.NUMLCT, C.ORILCT, C.CODFIL, C.DATLCT,
        C.CTADEB, C.CTACRE, C.VLRLCT,
        C.CODHPD, C.CPLLCT, C.NUMLOT, C.TEMRAT, C.SITLCT,
        C.OBSCPL, C.CODUSU, C.DATENT, C.HORENT, C.TEMAUX,
        C.CGCCPF, C.CGCCRE, C.NUMFTC, C.DATEXT
      FROM E640LCT C
      INNER JOIN E640LOT L WITH (NOLOCK) ON C.CODEMP = L.CODEMP AND C.CODFIL = L.CODFIL AND C.NUMLOT = L.NUMLOT 
      WHERE C.CODEMP = :codEmp AND C.SITLCT = 2 AND L.DATLOT BETWEEN :pd AND :ld
      ORDER BY C.DATLCT, L.DATLOT
    ";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute([
      ':codEmp' => $codEmpresa,
      ':pd'     => $primeiroDia,
      ':ld'     => $ultimoDia
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
