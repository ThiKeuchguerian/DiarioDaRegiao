<?php
require_once __DIR__ . '/../DBConnect.php';

class ContabilRecOperacionais
{
  private $senior;

  public function __construct()
  {
    $this->senior = DatabaseConnection::getConnection('senior');

    $this->senior->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
  }

  /**
   * Gera arrays [primeiroDia, ultimoDia] a partir de 'MM/YYYY'
   */
  public static function obterPrimeiroUltimoDia(string $mesAno): array
  {
    $data = \DateTime::createFromFormat('m/Y', $mesAno);
    if (! $data) {
      throw new \InvalidArgumentException("Mês/Ano inválido: {$mesAno}");
    }
    $primeiro = $data->format('Ym01');               // YYYY-mm-01
    $ultimo    = $data->modify('last day of this month')->format('Ymd');
    return [$primeiro, $ultimo];
  }

  /**
   * Método público que dispara todas as consultas e retorna um array com cada resultado
   */
  public function gerarRelatorio(string $mesAno, array $filtros): array
  {
    list($primeiroDia, $ultimoDia) = self::obterPrimeiroUltimoDia($mesAno);

    $todasConsultas = [
      'notasParaImportar'   => fn() => $this->consultaNotasParaImportar($primeiroDia, $ultimoDia),
      'mesComunicacao'      => fn() => $this->consultaMesComunicacao($primeiroDia, $ultimoDia),
      'outrosDocumentos'    => fn() => $this->consultaOutrosDocumentos($primeiroDia, $ultimoDia),
      'diferencaItens'      => fn() => $this->buscaDiferencaItens($primeiroDia, $ultimoDia),
      'diferencaOutrosDoc'  => fn() => $this->buscaDiferencaOutrosDoc($primeiroDia, $ultimoDia),
      'mesNotaFat'          => fn() => $this->consultaMesNotaFat($primeiroDia, $ultimoDia),
      'itensNotasFiscais'   => fn() => $this->consultaItensNotasFiscais($primeiroDia, $ultimoDia),
    ];

    // Se nenhum filtro for passado, executa todas
    if (empty($filtros)) {
      $filtros = array_keys($todasConsultas);
    }

    $resultado = [];

    foreach ($filtros as $filtro) {
      if (isset($todasConsultas[$filtro])) {
        $resultado[$filtro] = $todasConsultas[$filtro]();
      }
    }

    return $resultado;
  }

  private function consultaNotasParaImportar(string $primeiroDia, string $ultimoDia): array
  {
    $sql = "
      SELECT CODCLI, CODSNF, NUMNFV, CODCLI, DATEMI, TNSSER, VLRFIN
        FROM E140NFV
        WHERE CodEmp = 1
          AND CodSnf = 'NSC'
          AND SitNfv = '2'
          AND DATEMI BETWEEN :pd AND :ld
          AND TNSSER <> '7301'
          AND NOT EXISTS (
            SELECT 1
              FROM E660ODC o
            WHERE o.CodEmp  = E140NFV.codemp
              AND o.filnfv  = E140NFV.CodFil
              AND o.snfnfv  = E140NFV.codsnf
              AND o.numnfv  = E140NFV.numnfv
          )
        ORDER BY DATEMI
    ";
    $stmt = $this->senior->prepare($sql);
    // echo "<pre>";
    // var_dump($stmt);
    // var_dump($primeiroDia, $ultimoDia);
    // die();
    $stmt->execute([':pd' => $primeiroDia, ':ld' => $ultimoDia]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  private function consultaMesComunicacao(string $primeiroDia, string $ultimoDia): array
  {
    $sql = "
      WITH ConsultaItensPedido AS (
        SELECT   
          E120ISP.CODEMP, E120ISP.CODFIL, E120ISP.NUMPED, E120ISP.SEQISP, E120ISP.CODSER, E120ISP.UNIMED, 
          E120ISP.CPLISP, E120ISP.DATENT, E120ISP.QTDPED, E120ISP.QTDABE, E120ISP.PREUNI, E120ISP.VLRLIQ, 
          E120ISP.VLRFIN, E120ISP.QTDFAT, E120ISP.QTDCAN, E120ISP.CODTRI, E120ISP.SITISP, E120PED.CODCLI, 
          E120PED.CODREP, E120ISP.TNSSER, E085CLI.SIGUFS, E085CLI.CODRAM, E090HRP.CODRVE, E085CLI.CODGRE, 
          E120ISP.CODMOE, E120ISP.COTMOE, E120ISP.DATMOE, E120ISP.FECMOE, E120ISP.DATGER, E120ISP.CODTPR, 
          E120ISP.NUMPRJ, E120ISP.CODFPJ, E120ISP.CTAFIN, E120ISP.CTARED, E120ISP.CODCCU, E120ISP.PEDCLI, 
          E120ISP.SEQPCL, E120ISP.PERPIT, E120ISP.VLRPIT, E120ISP.PERCRT, E120ISP.VLRCRT, E120ISP.PERCSL, 
          E120ISP.VLRCSL, E120ISP.PEROUR, E120ISP.VLROUR, E120ISP.FILOCP, E120ISP.NUMOCP, E120ISP.SEQISO, 
          E120ISP.VLRDAR, E120ISP.VLRENC, E120ISP.VLROUT, E120ISP.VLRBRU, E120ISP.VLRDSC, E120ISP.PERDSC, 
          E120ISP.VLRDS1, E120ISP.VLRDS2, E120ISP.VLRDS3, E120ISP.VLRDS4, E120ISP.VLRDZF, E120ISP.VLRBIS, 
          E120ISP.VLRISS, E120ISP.PERISS, E120ISP.VLRBIR, E120ISP.VLRIRF, E120ISP.PERIRF, E120ISP.VLRBIN, 
          E120ISP.VLRINS, E120ISP.VLRBIP, E120ISP.VLRIPI, E120ISP.PERIPI, E120ISP.VLRICM, E120ISP.PERICM, 
          E120ISP.VLRBSI, E120ISP.VLRICS, E120ISP.VLRRIS, E120ISP.VLRBCT, E120ISP.VLRBPT, E120ISP.VLRBCL, 
          E120ISP.VLRBOR, E120ISP.VLRBCO, E120ISP.VLRCOM, E120ISP.VLRLSE, E120ISP.VLRLOU, E120ISP.VLRBIC, 
          E120ISP.PERCOM, E120PED.TIPPED, E120ISP.EMPOCP, E120ISP.VLRBPF, E120ISP.PERPIF, E120ISP.VLRPIF, 
          E120ISP.VLRBCF, E120ISP.PERCFF, E120ISP.VLRCFF, E120ISP.PERDS1, E120ISP.PERDS2, E120ISP.PERDS3, 
          E120ISP.PERDS4, E120ISP.QTDBPF, E120ISP.QTDBCF, E120ISP.QTDBIP, E120ISP.ALIPIF, E120ISP.ALIIPI, 
          E120ISP.ALICFF, E120ISP.VARSER, E120ISP.VLRPFM, E120ISP.FILREF, E120ISP.PEDREF, E120ISP.SEQREF, 
          E080SER.ITEFIS, E080SER.DESFIS, E120ISP.TABFRE, E120ISP.VLROUD, E120ISP.ICMAOR, E120ISP.ICMVOR,
          E120ISP.ICMADE, E120ISP.ICMVDE, E120ISP.ICMBDE, E120ISP.ICMAFC, E120ISP.ICMVFC, E120ISP.BASIDF, 
          E120ISP.PERIDF, E120ISP.VLRIDF, E120ISP.PERDIF, E120ISP.ICMBFC, E120ISP.BASFCP, E120ISP.ALIFCP, 
          E120ISP.VLRFCP, E120ISP.BSTFCP, E120ISP.ASTFCP, E120ISP.VSTFCP, E120ISP.VLRICD, E120ISP.MOTDES, 
          E120ISP.PDIFCP, E120ISP.VDIFCP, E120ISP.EFIFCP, E120ISP.VICSTD, E120ISP.MTDIST, E120ISP.NUMNFV, E120ISP.CODFAM
        FROM E120PED, E120ISP, E085CLI, E090HRP, E080SER 
        WHERE E120PED.CODEMP = 1 AND E090HRP.CODEMP = 1
          AND E120ISP.CODEMP = E120PED.CODEMP 
          AND E120ISP.CODFIL = E120PED.CODFIL 
          AND E120ISP.NUMPED = E120PED.NUMPED 
          AND E120PED.CODCLI = E085CLI.CODCLI 
          AND E120PED.CODREP = E090HRP.CODREP
          AND E080SER.CODEMP = E120ISP.CODEMP 
          AND E080SER.CODSER = E120ISP.CODSER
          AND E120ISP.DATENT BETWEEN :pd AND :ld
          AND E120PED.TIPPED = 1
          AND E120PED.CODFIL  = 1
          AND E120ISP.SITISP  = 4
          AND E120ISP.UNIMED <> 'SV'
      ), AjustandoItensPedido AS (
        SELECT 
          CODEMP, CODCLI, DATENT, NUMPED, SEQISP, CODSER, QTDFAT, PREUNI, CODREP, PEDCLI, (QTDFAT*PREUNI) AS ValorVeiculado, 
          CASE
            WHEN CODFAM = '08007' AND codcli = '46322' THEN '08007 - Receita Mec Externo'
            WHEN CODFAM = '08001'				  THEN '08001 - Noticiários' 
            WHEN CODFAM = '08002'				  THEN '08002 - Classificados CM' 
            WHEN CODFAM = '08003'				  THEN '08003 - Classificados Imóveis' 
            WHEN CODFAM = '08004'				  THEN '08004 - Classificados Linha' 
            WHEN CODFAM = '08005'				  THEN '08005 - Suplementos'         
            WHEN CODFAM = '08007'				  THEN '08007 - Diário Web' 
            WHEN CODFAM = '08009'				  THEN '08009 - Diário Imóveis' 
            WHEN CODFAM = '08010'				  THEN '08010 - Publicidade Redes Sociais' 
            ELSE '' 
          END TipoServico, FORMAT(DATENT, 'MM/yyyy') AS MesAno
        FROM ConsultaItensPedido
      ), Filtrando AS (
        SELECT CODEMP, CODCLI, NUMPED, DATENT, TipoServico, SEQISP, CODSER, ValorVeiculado, MesAno
        FROM AjustandoItensPedido
      )
        SELECT TipoServico, MesAno, 
          CAST(SUM(ValorVeiculado) AS DECIMAL(20,3)) AS VlrVeic
        FROM Filtrando
        GROUP BY TipoServico, MesAno
        ORDER BY TipoServico
    ";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute([':pd' => $primeiroDia, ':ld' => $ultimoDia]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  private function consultaOutrosDocumentos(string $primeiroDia, string $ultimoDia): array
  {
    $sql = "
      WITH ConsultaOutrosDoc AS (
        SELECT 
          odc.CODEMP, odc.CODFIL, odc.NUMDOC, odc.SEQDOC, odc.DATOPE, odc.CODFOR, odc.CODCLI, odc.ENTSAI, odc.CODTNS, odc.CODPRO, odc.CODDER, odc.CODSER, odc.DESDOC, 
          odc.VLROPE, odc.CTARED, odc.CODCCU, odc.CODDFS, odc.BASCRE, odc.CSTPIS, odc.PERPIS, odc.VLRBPI, odc.ALIPIS, odc.QTDBPI, odc.VLRPIS, odc.CSTCOF, odc.PERCOF, 
          odc.VLRBCF, odc.ALICOF, odc.QTDBCO, odc.VLRCOF, odc.VLRBPT, odc.VLRPIT, odc.VLRBCT, odc.VLRCRT, odc.ORIMIM, odc.EMPMCR, odc.FILMCR, odc.NUMMCR, odc.TPTMCR, 
          odc.SEQMCR, odc.EMPMCP, odc.FILMCP, odc.NUMMCP, odc.TPTMCP, odc.FORMCP, odc.SEQMCP, odc.EMPMCC, odc.NUMMCC, odc.DATMCC, odc.SEQMCC, odc.NATPIS, odc.NATCOF, 
          odc.FILCON, odc.USUCON, odc.DATCON, odc.HORCON, odc.CODFCT, odc.NUMLOT, odc.VLRBIR, odc.VLRIRF, odc.VLRBCL, odc.VLRCSL, odc.FILNFC, odc.FORNFC, odc.NUMNFC, 
          odc.SNFNFC, odc.FILNFV, odc.SNFNFV, odc.NUMNFV, odc.SEQITP, odc.SEQITS, odc.IDEGED, odc.NUMDOF
        FROM E660ODC odc
        LEFT OUTER JOIN E075PRO ON E075PRO.CODEMP = odc.CODEMP AND E075PRO.CODPRO = odc.CODPRO 
        LEFT OUTER JOIN E075DER ON E075DER.CODEMP = odc.CODEMP AND E075DER.CODPRO = odc.CODPRO AND E075DER.CODDER = odc.CODDER 
        LEFT OUTER JOIN E080SER ON E080SER.CODEMP = odc.CODEMP AND E080SER.CODSER = odc.CODSER 
        WHERE odc.CODEMP = 1 AND odc.CODFIL = 1
        AND odc.DATOPE BETWEEN :pd AND :ld
      ), AjustandoOutroDoc AS (
        SELECT CODEMP, CODCLI, NUMDOC, DATOPE, SEQDOC, CODSER, VLROPE,
          CASE
            WHEN codser LIKE '08007%' AND codcli = '46322' THEN '08007 - Receita Mec Externo'
            WHEN codser LIKE '08001%'				               THEN '08001 - Noticiários' 
            WHEN codser LIKE '08002%'				               THEN '08002 - Classificados CM' 
            WHEN codser LIKE '08003%'				               THEN '08003 - Classificados Imóveis'  
            WHEN codser LIKE '08004%'				               THEN '08004 - Classificados Linha' 
            WHEN codser LIKE '08005%'				               THEN '08005 - Suplementos'  
            WHEN codser LIKE '08007%'				               THEN '08007 - Diário Web' 
            WHEN codser LIKE '08009%'				               THEN '08009 - Diário Imóveis' 
            WHEN codser LIKE '08010%'				               THEN '08010 - Publicidade Redes Sociais'
            ELSE '' 
          END TipoServico, FORMAT(DATOPE, 'MM/yyyy') AS MesAno
        FROM ConsultaOutrosDoc
      ), Filtrando AS (
        SELECT CODEMP, CODCLI, NUMDOC, DATOPE, TipoServico, SEQDOC, CODSER, VLROPE, MesAno
        FROM AjustandoOutroDoc
        WHERE TipoServico <> ''
      )
      SELECT TipoServico, MesAno, SUM(VLROPE) AS VlrVeic
      FROM Filtrando
      GROUP BY TipoServico, MesAno
      ORDER BY TipoServico
    ";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute([':pd' => $primeiroDia, ':ld' => $ultimoDia]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  private function buscaDiferencaItens(string $primeiroDia, string $ultimoDia): array
  {
    $sql = "
      WITH ConsultaItensPedido AS (
        SELECT   
          E120ISP.CODEMP, E120ISP.CODFIL, E120ISP.NUMPED, E120ISP.SEQISP, E120ISP.CODSER, E120ISP.UNIMED, 
          E120ISP.CPLISP, E120ISP.DATENT, E120ISP.QTDPED, E120ISP.QTDABE, E120ISP.PREUNI, E120ISP.VLRLIQ, 
          E120ISP.VLRFIN, E120ISP.QTDFAT, E120ISP.QTDCAN, E120ISP.CODTRI, E120ISP.SITISP, E120PED.CODCLI, 
          E120PED.CODREP, E120ISP.TNSSER, E085CLI.SIGUFS, E085CLI.CODRAM, E090HRP.CODRVE, E085CLI.CODGRE, 
          E120ISP.CODMOE, E120ISP.COTMOE, E120ISP.DATMOE, E120ISP.FECMOE, E120ISP.DATGER, E120ISP.CODTPR, 
          E120ISP.NUMPRJ, E120ISP.CODFPJ, E120ISP.CTAFIN, E120ISP.CTARED, E120ISP.CODCCU, E120ISP.PEDCLI, 
          E120ISP.SEQPCL, E120ISP.PERPIT, E120ISP.VLRPIT, E120ISP.PERCRT, E120ISP.VLRCRT, E120ISP.PERCSL, 
          E120ISP.VLRCSL, E120ISP.PEROUR, E120ISP.VLROUR, E120ISP.FILOCP, E120ISP.NUMOCP, E120ISP.SEQISO, 
          E120ISP.VLRDAR, E120ISP.VLRENC, E120ISP.VLROUT, E120ISP.VLRBRU, E120ISP.VLRDSC, E120ISP.PERDSC, 
          E120ISP.VLRDS1, E120ISP.VLRDS2, E120ISP.VLRDS3, E120ISP.VLRDS4, E120ISP.VLRDZF, E120ISP.VLRBIS, 
          E120ISP.VLRISS, E120ISP.PERISS, E120ISP.VLRBIR, E120ISP.VLRIRF, E120ISP.PERIRF, E120ISP.VLRBIN, 
          E120ISP.VLRINS, E120ISP.VLRBIP, E120ISP.VLRIPI, E120ISP.PERIPI, E120ISP.VLRICM, E120ISP.PERICM, 
          E120ISP.VLRBSI, E120ISP.VLRICS, E120ISP.VLRRIS, E120ISP.VLRBCT, E120ISP.VLRBPT, E120ISP.VLRBCL, 
          E120ISP.VLRBOR, E120ISP.VLRBCO, E120ISP.VLRCOM, E120ISP.VLRLSE, E120ISP.VLRLOU, E120ISP.VLRBIC, 
          E120ISP.PERCOM, E120PED.TIPPED, E120ISP.EMPOCP, E120ISP.VLRBPF, E120ISP.PERPIF, E120ISP.VLRPIF, 
          E120ISP.VLRBCF, E120ISP.PERCFF, E120ISP.VLRCFF, E120ISP.PERDS1, E120ISP.PERDS2, E120ISP.PERDS3, 
          E120ISP.PERDS4, E120ISP.QTDBPF, E120ISP.QTDBCF, E120ISP.QTDBIP, E120ISP.ALIPIF, E120ISP.ALIIPI, 
          E120ISP.ALICFF, E120ISP.VARSER, E120ISP.VLRPFM, E120ISP.FILREF, E120ISP.PEDREF, E120ISP.SEQREF, 
          E080SER.ITEFIS, E080SER.DESFIS, E120ISP.TABFRE, E120ISP.VLROUD, E120ISP.ICMAOR, E120ISP.ICMVOR,
          E120ISP.ICMADE, E120ISP.ICMVDE, E120ISP.ICMBDE, E120ISP.ICMAFC, E120ISP.ICMVFC, E120ISP.BASIDF, 
          E120ISP.PERIDF, E120ISP.VLRIDF, E120ISP.PERDIF, E120ISP.ICMBFC, E120ISP.BASFCP, E120ISP.ALIFCP, 
          E120ISP.VLRFCP, E120ISP.BSTFCP, E120ISP.ASTFCP, E120ISP.VSTFCP, E120ISP.VLRICD, E120ISP.MOTDES, 
          E120ISP.PDIFCP, E120ISP.VDIFCP, E120ISP.EFIFCP, E120ISP.VICSTD, E120ISP.MTDIST, E140ISV.NUMNFV, E120ISP.CODFAM
        FROM E120PED, E120ISP, E085CLI, E090HRP, E080SER, E140ISV
        WHERE E120PED.CODEMP = 1 
          AND E120ISP.CODEMP = E120PED.CODEMP 
          AND E120ISP.CODFIL = E120PED.CODFIL 
          AND E120ISP.NUMPED = E120PED.NUMPED 
          AND E120PED.CODCLI = E085CLI.CODCLI 
          AND E120PED.CODREP = E090HRP.CODREP 
          AND E090HRP.CODEMP = 1
          AND E080SER.CODEMP = E120ISP.CODEMP 
          AND E080SER.CODSER = E120ISP.CODSER
          AND (E120PED.codemp = E140ISV.codemp AND E120PED.codfil = E140ISV.codfil AND E120PED.numped = E140ISV.numped AND E120ISP.SEQISP = E140ISV.SEQISP)
          AND E120ISP.DATENT BETWEEN :pd AND :ld
          AND (( (E120PED.TIPPED = 1) )) 
          AND (( (E120PED.CODFIL = 1) )) 
          AND ((E120ISP.SITISP = 4)) 
          AND E120ISP.UNIMED <> 'SV'
      ), AjustandoItensPedido AS (
        SELECT 
          CODEMP, CODCLI, DATENT, NUMPED, SEQISP, CODSER, QTDFAT, PREUNI, CODREP, PEDCLI, (QTDFAT*PREUNI) AS ValorVeiculado, NUMNFV,
          CASE
            WHEN CODFAM = '08007' AND codcli = '46322' THEN '08007 - Receita Mec Externo'
            WHEN CODFAM = '08001'				               THEN '08001 - Noticiários' 
            WHEN CODFAM = '08002'				               THEN '08002 - Classificados CM' 
            WHEN CODFAM = '08003'				               THEN '08003 - Classificados Imóveis' 
            WHEN CODFAM = '08004'				               THEN '08004 - Classificados Linha' 
            WHEN CODFAM = '08005'				               THEN '08005 - Suplementos'         
            WHEN CODFAM = '08007'				               THEN '08007 - Diário Web' 
            WHEN CODFAM = '08009'				               THEN '08009 - Diário Imóveis' 
            WHEN CODFAM = '08010'				               THEN '08010 - Publicidade Redes Sociais' 
            ELSE '' 
          END TipoServico, FORMAT(DATENT, 'MM/yyyy') AS MesAno
        FROM ConsultaItensPedido
      )
      SELECT
        CODCLI, NUMPED, NUMNFV, PEDCLI, DATENT, TipoServico, CODSER,
        CAST(ValorVeiculado AS DECIMAL(18,2)) AS VlrVeiculado,
        ROW_NUMBER() OVER (PARTITION BY NUMNFV ORDER BY NUMPED) AS SEQISP
      FROM AjustandoItensPedido
        WHERE TipoServico <> ''
      ORDER BY CODCLI, NUMNFV, DATENT, TipoServico, CODSER, ValorVeiculado
    ";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute([':pd' => $primeiroDia, ':ld' => $ultimoDia]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  private function buscaDiferencaOutrosDoc(string $primeiroDia, string $ultimoDia): array
  {
    $sql = "
      WITH ConsultaOutrosDoc AS (
        SELECT 
          odc.CODEMP, odc.CODFIL, odc.NUMDOC, odc.SEQDOC, odc.DATOPE, odc.CODFOR, odc.CODCLI, odc.ENTSAI, odc.CODTNS, odc.CODPRO, odc.CODDER, odc.CODSER, odc.DESDOC, 
          odc.VLROPE, odc.CTARED, odc.CODCCU, odc.CODDFS, odc.BASCRE, odc.CSTPIS, odc.PERPIS, odc.VLRBPI, odc.ALIPIS, odc.QTDBPI, odc.VLRPIS, odc.CSTCOF, odc.PERCOF, 
          odc.VLRBCF, odc.ALICOF, odc.QTDBCO, odc.VLRCOF, odc.VLRBPT, odc.VLRPIT, odc.VLRBCT, odc.VLRCRT, odc.ORIMIM, odc.EMPMCR, odc.FILMCR, odc.NUMMCR, odc.TPTMCR, 
          odc.SEQMCR, odc.EMPMCP, odc.FILMCP, odc.NUMMCP, odc.TPTMCP, odc.FORMCP, odc.SEQMCP, odc.EMPMCC, odc.NUMMCC, odc.DATMCC, odc.SEQMCC, odc.NATPIS, odc.NATCOF, 
          odc.FILCON, odc.USUCON, odc.DATCON, odc.HORCON, odc.CODFCT, odc.NUMLOT, odc.VLRBIR, odc.VLRIRF, odc.VLRBCL, odc.VLRCSL, odc.FILNFC, odc.FORNFC, odc.NUMNFC, 
          odc.SNFNFC, odc.FILNFV, odc.SNFNFV, odc.NUMNFV, odc.SEQITP, odc.SEQITS, odc.IDEGED, odc.NUMDOF
        FROM E660ODC odc
        LEFT OUTER JOIN E075PRO ON E075PRO.CODEMP = odc.CODEMP AND E075PRO.CODPRO = odc.CODPRO 
        LEFT OUTER JOIN E075DER ON E075DER.CODEMP = odc.CODEMP AND E075DER.CODPRO = odc.CODPRO AND E075DER.CODDER = odc.CODDER 
        LEFT OUTER JOIN E080SER ON E080SER.CODEMP = odc.CODEMP AND E080SER.CODSER = odc.CODSER 
        WHERE odc.CODEMP = 1 AND odc.CODFIL = 1
        AND odc.DATOPE BETWEEN :pd AND :ld
      ), AjustandoOutroDoc AS (
        SELECT CODEMP, CODCLI, NUMDOC, DATOPE, SEQDOC, CODSER, VLROPE, NUMNFV,
          CASE
            WHEN codser LIKE '08007%' AND codcli = '46322' THEN '08007 - Receita Mec Externo'
            WHEN codser LIKE '08001%'				               THEN '08001 - Noticiários' 
            WHEN codser LIKE '08002%'				               THEN '08002 - Classificados CM' 
            WHEN codser LIKE '08003%'				               THEN '08003 - Classificados Imóveis'  
            WHEN codser LIKE '08004%'				               THEN '08004 - Classificados Linha' 
            WHEN codser LIKE '08005%'				               THEN '08005 - Suplementos'  
            WHEN codser LIKE '08007%'				               THEN '08007 - Diário Web' 
            WHEN codser LIKE '08009%'				               THEN '08009 - Diário Imóveis' 
            WHEN codser LIKE '08010%'				               THEN '08010 - Publicidade Redes Sociais'
            ELSE '' 
          END TipoServico, FORMAT(DATOPE, 'MM/yyyy') AS MesAno
        FROM ConsultaOutrosDoc
      )
        SELECT CODCLI, NUMDOC, SEQDOC, NUMNFV, DATOPE, TipoServico, CODSER, VLROPE
        FROM AjustandoOutroDoc
        WHERE TipoServico <> ''
        ORDER BY CODCLI, NUMNFV, DATOPE, TipoServico, CODSER, VLROPE
    ";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute([':pd' => $primeiroDia, ':ld' => $ultimoDia]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  private function consultaMesNotaFat(string $primeiroDia, string $ultimoDia): array
  {
    $sql = "
      WITH ItensNota AS (
        SELECT NF.codemp AS CodEmp, NF.numnfv AS NumNota, NF.tnspro AS TnsPro, NF.tnsser AS TnsSer, 
          NF.codcli AS CodCli, NF.vlrbcf AS VlrBruto, NF.vlrliq AS VlrLiq, NF.sitnfv AS Status, INF.tnsser AS Tns, 
          INF.codfam AS CodFam, INF.codser AS CodSerPro, INF.qtdfat AS QtdeFat, INF.preuni AS VlrUni, INF.unimed AS Unimed,
          CASE
            WHEN INF.codfam = '09001' THEN 'Encartes 5949S'
            WHEN NF.tnsser = '5101I' OR NF.tnsser = '5101T' OR NF.tnspro = '6101I' OR NF.tnspro = '6101T' THEN 'Impressos Embalagens 5101I/5101T'
            WHEN (NF.tnsser = '5101' AND INF.codfam = '10002') OR (NF.tnsser = '6101' AND INF.codfam = '10002') THEN 'Impressos de Jornais 5101/6101'
            WHEN NF.tnsser = '5116' OR NF.tnsser = '5116E' THEN 'Assinaturas de Jornais  5116/5116E'
            WHEN (NF.tnspro = '5113' AND INF.codfam = '10001') OR (NF.tnspro = '5101' AND INF.codfam = '10001') THEN 'Vendas Avulsos 5101/5113'
            WHEN NF.tnsser = '5949S' OR NF.tnsser = '6949S' THEN 'Impressos Comerciais 5949S/6949S'
            WHEN (NF.tnspro = '5101E' AND INF.codfam = '10004')  THEN 'Sucatas 5101E'
            ELSE ''
          END TipoServico, FORMAT (NF.datemi, 'MM/yyyy') AS MesAno,
          ROW_NUMBER() OVER (PARTITION BY NF.numnfv ORDER BY NF.datemi) AS RowNum
        FROM e140nfv NF
          INNER JOIN e140isv INF WITH (NOLOCK) ON NF.numnfv = INF.numnfv
          WHERE NF.datemi BETWEEN :pd AND :ld
            AND NF.sitnfv = '2'
        UNION ALL
        SELECT NF.codemp AS CodEmp, NF.numnfv AS NumNota, NF.tnspro AS TnsPro, NF.tnsser AS TnsSer, NF.codcli AS CodCli, 
          NF.vlrbcf AS VlrBruto, NF.vlrliq AS VlrLiq, NF.sitnfv AS Status, INF.tnspro AS Tns, INF.codfam AS CodFam, 
          INF.codpro AS CodSerPro, INF.qtdfat AS QtdeFat, INF.preuni AS VlrUni, INF.unimed AS Unimed,
          CASE
            WHEN INF.codfam = '09001' THEN 'Encartes 5949S'
            WHEN 
              (NF.tnspro = '5101I' AND INF.codfam = '10001') OR 
              (NF.tnspro = '5101T' AND INF.codfam = '10001') OR 
              (NF.tnspro = '6101I' AND INF.codfam = '10001') OR 
              (NF.tnspro = '6101T' AND INF.codfam = '10001') 
            THEN 'Impressos Embalagens 5101I/5101T'
            WHEN (NF.tnspro = '5101' AND INF.codfam = '10002') OR (NF.tnspro = '6101' AND INF.codfam = '10002') THEN 'Impressos de Jornais 5101/6101'
            WHEN NF.tnspro = '5116' OR NF.tnspro = '5116E' THEN 'Assinaturas de Jornais  5116/5116E'
            WHEN (NF.tnspro = '5113' AND INF.codfam = '10001') OR (NF.tnspro = '5101' AND INF.codfam = '10001') THEN 'Vendas Avulsos 5101/5113'
            WHEN NF.tnspro = '5949S' OR NF.tnspro = '6949S' THEN 'Impressos Comerciais 5949S/6949S'
            WHEN(NF.tnspro = '5101E' AND INF.codfam = '10004')  THEN 'Sucatas 5101E'
            ELSE ''
          END TipoServico, FORMAT (NF.datemi, 'MM/yyyy') AS MesAno,
          ROW_NUMBER() OVER (PARTITION BY NF.numnfv ORDER BY NF.datemi) AS RowNum
        FROM e140nfv NF
          INNER JOIN e140ipv INF WITH (NOLOCK) ON NF.numnfv = INF.numnfv
          WHERE NF.datemi BETWEEN :pd AND :ld AND NF.sitnfv = '2'
        ), Agrupando AS (
          SELECT CodEmp, NumNota, TnsPro, TnsSer, CodCli, QtdeFat, Status, CodFam, CodSerPro, VlrUni, UniMed, 
            CASE 
              WHEN TipoServico = 'Impressos Embalagens 5101I/5101T' THEN VlrLiq
              ELSE VlrBruto
            END AS VlrBruto, TipoServico, MesAno, RowNum
          FROM ItensNota
            WHERE TipoServico <> '' AND RowNum = '1'
          GROUP BY CodEmp, NumNota, TnsPro, TnsSer, CodCli, QtdeFat, Status, CodFam, CodSerPro, VlrUni, UniMed, VlrBruto, VlrLiq, TipoServico, MesAno, RowNum
        )
        SELECT TipoServico, MesAno, ROUND(SUM(VlrBruto),2) AS VlrBruto
        FROM Agrupando
         WHERE TipoServico <> ''
         GROUP BY TipoServico, MesAno
         ORDER BY TipoServico
    ";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute([':pd' => $primeiroDia, ':ld' => $ultimoDia]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  private function consultaItensNotasFiscais(string $primeiroDia, string $ultimoDia): array
  {
    $sql = "
      WITH ConsultaItensNotaFiscais AS (
        SELECT E660INV.CODCLI AS CLIFOR, E660INV.NUMNFI, E660INV.NUMNFF, E660INV.CODSNF, E660INV.CODTNS, 
          E660INV.SEQINV AS SEQINX, E660INV.SEQIPV AS SEQIPX, E660INV.CODPRO, E660INV.CODSER, E660INV.CODBEM, 
          E660INV.CODDER, E660INV.CPLPRO, E660INV.CODCLF, E660INV.CLAFIS, E660INV.QTDENT, E660INV.UNIMED, 
          E660INV.VLRCTB, E660INV.PERIPI, E660INV.VLRBIP, E660INV.VLRIPI, E660INV.VLRIIP, E660INV.VLROIP, 
          E660INV.VLRBID, E660INV.VLRIPD, E660INV.PERICM, E660INV.VLRBIC, E660INV.VLRICM, E660INV.VLRIIC, 
          E660INV.VLROIC, E660INV.VLRDAI, E660INV.VLRRIS, E660INV.VLRBSI, E660INV.VLRSIC, E660INV.VLRDSC, 
          E660INV.CODSTR, E660INV.CODTRD, E660INV.CTARED, E660INV.CODCCU, E660INV.CODTST, E660INV.VLRDAC, 
          E660INV.PERIIM, E660INV.VLRBII, E660INV.VLRIIM, E660INV.VLRBSD, E660INV.VLRISD, E660INV.VLRBSP, 
          E660INV.VLRSTP, E660INV.VLRBSC, E660INV.VLRSTC, E660INV.PERISS, E660INV.VLRBIS, E660INV.VLRISS, 
          E660INV.VLRBIR, E660INV.VLRIRF, E660INV.PERIRF, E660INV.PERFUN, E660INV.VLRBFU, E660INV.VLRFUN, 
          E660INV.PERINS, E660INV.VLRBIN, E660INV.VLRINS, E660INV.VLRFRE, E660INV.VLRSEG, E660INV.VLRBPR, 
          E660INV.VLRPIR, E660INV.VLRBCR, E660INV.VLRCOR, E660INV.PERCRT, E660INV.VLRBCT, E660INV.VLRCRT, 
          E660INV.PERPIT, E660INV.VLRBPT, E660INV.VLRPIT, E660INV.PERCSL, E660INV.VLRBCL, E660INV.VLRCSL, 
          E660INV.PEROUR, E660INV.VLRBOR, E660INV.VLROUR, E660INV.PERPIF, E660INV.VLRBPF, E660INV.VLRPIF, 
          E660INV.PERCFF, E660INV.VLRBCF, E660INV.VLRCFF, E660INV.USUGER, E660INV.DATGER, E660INV.HORGER, 
          E660INV.USUATU, E660INV.DATATU, E660INV.HORATU, E660INV.CSTIPI, E660INV.CSTPIS, E660INV.CSTCOF, 
          'S' AS ENTSAI, E660NFV.NOPOPE, E660NFV.DATEMI AS DATMOV, 0 AS BCOIMP, 0 AS COFIMP, 0 AS BPIIMP, 
          0 AS PISIMP, E660INV.TOTCID, E660INV.VLRCID, E660INV.VLRDZF, 0 AS NUMADI, 0 AS SEQADI, 0 AS DSCADI, 
          '' AS FABEST, E660INV.PREUNI, E660INV.QTDBIP, E660INV.ALIIPI, E660INV.QTDBPI, E660INV.ALIPIS, 
          E660INV.QTDBCO, E660INV.ALICOF, E660INV.QTDBPF, E660INV.ALIPIF, E660INV.QTDBCF, E660INV.ALICFF, 
          0 AS VLRCIP, E660INV.CODFIL, E660INV.VLRDZP, E660INV.VLRDZC, E660INV.CODEMP, E660INV.SEQNFI, 
          E660INV.DESIMP, E660INV.ORIMER, E660INV.PERPIR, E660INV.PERCOR, E660INV.CODMS1, E660INV.CODMS2, 
          E660INV.CODMS3, E660INV.CODMS4, E660INV.VLRSUB, E660INV.NUMRDE, E660INV.NATPIS, E660INV.NATCOF, 
          0 AS VLRFEI, 0 AS VLRSEI, 0 AS VLROUI, 0 AS VLRBIE, 0 AS VLRIEM, 0 AS VLRIOP, 0 AS VLRIST, 0 AS VLRIDF, 
          '' AS INTPAT, 0 AS NFIPRO, 0 AS PESENT, 0 AS BASDIF, 0 AS DIFPES, '' AS SITCIP, '' AS OBSINA, 0 AS TOTCIT, 
          0 AS PERCIT, E660IDE.SITDOE, '' AS SITNFC, E660IDE.CHVDOE, '' AS CHVNEL, 0 AS TIPMOV, E660IDE.NUMDFS, 
          0 AS NUMDFSNFC, E660IDE.CODVER, E660IDE.NUMPRT, 0 AS VLRIPN, 0 AS VLRICN, E660INV.VLRMRC, 
          E075DER.ITEFIS AS ITFPRO, E080SER.ITEFIS AS ITFSER, E075DER.DESFIS AS DEFPRO, E080SER.DESFIS AS DEFSER, 
          0 AS PERCIM, 0 AS PERPIM, 0 AS BASCRE, 0 AS VLRBRI, E660INV.PERSTP, E660INV.PERSTC, '' AS NUMDRB, 
          E660INV.VLRIDV, 0 AS VLRAFM, E660INV.PERSEN, E660INV.VLRBSN, E660INV.VLRSEN, E660INV.VLRICD, E660INV.CODENQ, 
          E660INV.ICMAOR, E660INV.ICMVOR, E660INV.ICMADE, E660INV.ICMVDE, E660INV.ICMBDE, E660INV.ICMAFC, E660INV.ICMVFC, 
          E660INV.CODCES, 0 AS VLRIBS, 0 AS VLRISN, E660INV.VLRDED, 0 AS ACOIMP, 0 AS APIIMP, 0 AS QTDCIM, 0 AS QTDPIM, 
          E660INV.ICMBFC, E660INV.BASFCP, E660INV.ALIFCP, E660INV.VLRFCP, E660INV.BSTFCP, E660INV.ASTFCP, E660INV.VSTFCP, 
          E660INV.BREFCP, E660INV.AREFCP, E660INV.VREFCP, E660INV.PERGIL, E660INV.PERAPE, E660INV.VLRGIL, E660INV.VLRAPE, 
          E660INV.VLRBGI, E660INV.BASAPE, E660INV.VLRIMP, 0 AS PERIDO, 0 AS BASIDO, 0 AS VLRIDO, E660INV.QTMBIC, 
          E660INV.VMOICM, E660INV.QTMBIR, E660INV.VMOICR, E660INV.QTMBIF, E660INV.VMOICF, E660INV.QTMBID, E660INV.VMOICD, 
          E660INV.ALIIMO, E660INV.ALIIMR, E660INV.ALIIMF, E660INV.ALIIMD, E660INV.ALIMOR, 0 AS VLRISC, 
          CASE 
            WHEN E075PRO.CODFAM IS NULL THEN E080SER.CODFAM
            ELSE E075PRO.CODFAM
          END AS FAMPROSER, FORMAT(E660NFV.DATEMI, 'MM/yyyy') AS MesAno, E085CLI.NOMCLI
        FROM E660INV 
          LEFT OUTER JOIN E075PRO ON E075PRO.CODEMP = E660INV.CODEMP AND E075PRO.CODPRO = E660INV.CODPRO 
          LEFT OUTER JOIN E075DER ON E075DER.CODEMP = E660INV.CODEMP AND E075DER.CODPRO = E660INV.CODPRO AND E075DER.CODDER = E660INV.CODDER 
          LEFT OUTER JOIN E080SER ON E080SER.CODEMP = E660INV.CODEMP AND E080SER.CODSER = E660INV.CODSER, E085CLI, E020SNF , E660NFV 
          LEFT OUTER JOIN E660IDE ON E660IDE.CODEMP = E660NFV.CODEMP AND E660IDE.CODSNF = E660NFV.CODSNF AND E660IDE.CODFIL = E660NFV.CODFIL AND E660IDE.NUMNFI = E660NFV.NUMNFI 
        WHERE E660NFV.CODEMP = 1 AND E660NFV.CODFIL = 1
          AND E660NFV.CODEMP = E660INV.CODEMP AND E660NFV.CODFIL = E660INV.CODFIL 
          AND E660NFV.CODCLI = E660INV.CODCLI AND E660NFV.NUMNFI = E660INV.NUMNFI 
          AND E660NFV.NUMNFF = E660INV.NUMNFF AND E660NFV.CODSNF = E660INV.CODSNF 
          AND E660NFV.CODTNS = E660INV.CODTNS AND E085CLI.CODCLI = E660NFV.CODCLI 
          AND E020SNF.CODEMP = E660NFV.CODEMP AND E020SNF.CODFIL = E660NFV.CODFIL 
          AND E020SNF.CODSNF = E660NFV.CODSNF  
          AND E660NFV.DATEMI BETWEEN :pd AND :ld
      ), Filtrando AS (
        SELECT NUMNFI, CLIFOR, NOMCLI, CODSNF, CODTNS, CPLPRO, FAMPROSER, MesAno,
          CASE
            WHEN FAMPROSER LIKE '09001' THEN 'Encartes 5949S'
            WHEN CODTNS = '5101I' OR CODTNS = '5101T' OR CODTNS = '6101I' OR CODTNS = '6101T' THEN 'Impressos Embalagens 5101I/5101T'
            WHEN (CODTNS = '5101' AND FAMPROSER LIKE '10002') OR (CODTNS = '6101' AND FAMPROSER LIKE '10002') THEN 'Impressos de Jornais 5101/6101'
            WHEN CODTNS = '5116' OR CODTNS = '5116E' THEN 'Assinaturas de Jornais  5116/5116E'
            WHEN (CODTNS = '5113' AND FAMPROSER LIKE '10001') OR (CODTNS = '5101' AND FAMPROSER LIKE '10001') THEN 'Vendas Avulsos 5101/5113'
            WHEN CODTNS = '5949S' OR CODTNS = '6949S' THEN 'Impressos Comerciais 5949S/6949S'
            WHEN (CODTNS = '5101E' AND FAMPROSER LIKE '10004') THEN 'Sucatas 5101E'
            ELSE ''
          END AS TipoServico, VLRCTB, VLROIP, VLROIC, PREUNI, VLRMRC
        FROM ConsultaItensNotaFiscais
      )
      SELECT TipoServico, MesAno, ROUND(SUM(VLRCTB), 2) AS Valor
      FROM Filtrando
        WHERE TipoServico <> ''
      GROUP BY TipoServico, MesAno
      ORDER BY TipoServico
    ";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute([':pd' => $primeiroDia, ':ld' => $ultimoDia]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }
}
