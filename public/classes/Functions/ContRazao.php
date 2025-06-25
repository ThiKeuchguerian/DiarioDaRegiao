<?php

require_once __DIR__ . '/../DBConnect.php';

class ContabilRazao
{
  private $senior;

  public function __construct()
  {
    $this->senior = DatabaseConnection::getConnection('senior');

    $this->senior->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
  }

  /**
   * Busca lançamentos de razão conforme filtros informados.
   *
   * @param PDO         $conn      conexão PDO
   * @param int         $codEmp    código da empresa (sempre obrigatório)
   * @param string|null $ctaRed    conta reduzida (ex: '1.1.01.01')
   * @param string|null $mesAno    mês/ano no formato 'MM/YYYY'
   * @param string|null $dtInicio  data início no formato 'YYYY-MM-DD' ou 'DD/MM/YYYY'
   * @param string|null $dtFim     data fim no formato 'YYYY-MM-DD' ou 'DD/MM/YYYY'
   * @return array                resultado fetchAll(PDO::FETCH_ASSOC)
   */
  public function consultaRazao(
    int     $codEmp,
    ?string $ctaRed   = null,
    ?string $dtInicio = null,
    ?string $dtFim    = null,
    ?string $mesAno   = null
  ): array {
    $sql = "SELECT PLA.CTARED, PLA.NIVCTA, PLA.MSKGCC, PLA.CLACTA, PLA.SITCTA, PLA.DESCTA, LCT.DATLCT, LCT.NUMLCT,
        LCT.NUMLOT, LCT.VLRLCT, LCT.CTACRE, LCT.CTADEB, LCT.CODHPD, LCT.CPLLCT, LCT.OBSCPL, LCT.ORILCT, LCT.TIPLCT
      FROM E045PLA AS PLA
      LEFT JOIN E640LCT AS LCT ON LCT.CODEMP = PLA.CODEMP 
        AND LCT.SITLCT = 2 
        AND LCT.TIPLCT IN (1,3)
        AND (LCT.CTACRE = PLA.CTARED OR LCT.CTADEB = PLA.CTARED)
    ";

    $where  = [];
    $query  = [];
    $params = [];
    
    // Filtro Empresa
    if (!empty($codEmp)) {
      $where[]           = "PLA.CODEMP = :codEmp";
      $params[':codEmp'] = $codEmp;
    }
    // Filtro CtaRed
    if (!empty($ctaRed)) {
      $query[]           = "PLA.CTARED = :ctaRed";
      $params[':ctaRed'] = $ctaRed;
    }
    
    // Filtro intervalo de datas
    if (!empty($dtInicio) && !empty($dtFim)) {
      // converte para YYYYMMDD, formato unívoco no SQL-Server
      $d1 = (new DateTime($dtInicio))->format('Ymd');
      $d2 = (new DateTime($dtFim))->format('Ymd');
      $query[]              = "LCT.DATLCT BETWEEN :dtInicio AND :dtFim";
      $params[':dtInicio']  = $d1;
      $params[':dtFim']     = $d2;
    }
    
    // Filtro Mes/Ano
    if (!empty($mesAno)) {
      $query[]           = "FORMAT(LCT.DATLCT, 'MM/yyyy') = :mesAno";
      $params[':mesAno'] = $mesAno;
    }

    //Monta where
    if (count($query) > 0) {
      $sql .= "\n AND " . implode("\n AND ", $query) . "\n WHERE " . implode($where);
    }

    // Finaliza e Ordena
    $sql .= "\n AND NUMLCT IS NOT NULL ORDER BY LCT.DATLCT, LCT.NUMLOT, LCT.NUMLCT";

    // Debugando
    // echo "<pre>";
    // var_dump($sql);
    // var_dump($codEmp, $ctaRed, $dtInicio, $dtFim, $mesAno);
    // die();

    // Preparando e executa
    $stmt = $this->senior->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchALl(PDO::FETCH_ASSOC);
  }

}
