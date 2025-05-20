<?php
require_once __DIR__ . '/../DBConnect.php';

class GraficaComissao
{
  private $senior;

  public function __construct()
  {
    $this->senior = DatabaseConnection::getConnection('senior');

    $this->senior->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
  }

  public function consultaVendedor(): array
  {
    $sql = "SELECT aperep, usu_iderep FROM e090rep WHERE usu_iderep IN (300,736,1417,1455,1459,1460,1463,1464,1465,1466,1468,1469,1470,1471,1474) ORDER BY aperep";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  /**
   * Retorna o relatório PIVOT das metas gráficas para o ano e tipo
   *
   * @param string $DtInicio ex: "YYYY-MM-DD"
   * @param string $DtFim    ex: "YYYY-MM-DD"
   * @return array           linhas já formatadas
   */
  public function consultaComissao(string $dtInicio, string $dtFim, int $codVen): array
  {
    if (isset($dtInicio) && isset($dtFim)) {
      $dtInicio = str_replace('-', '', $dtInicio);
      $dtFim = str_replace('-', '', $dtFim);
    }
    // echo "<pre>";
    // var_dump($dtInicio, $dtFim, $codVen);
    // die();

    $queryPadrao =
      " SELECT Ped.pedcli NumPedOrc, NF.codcli AS CodCli,
          FORMAT(Mcr.datpgt, 'MM/yyyy') MesAnoPgto, Ped.datemi AS DtPedido, NF.datemi AS DtNF,
          Cli.cgccpf AS CpfCnpj, Ped.numped AS NumPedido,
          CASE 
            WHEN LTRIM(RTRIM(ISNULL(Cli.apecli, ''))) = '' THEN RTRIM(Cli.nomcli)
            ELSE RTRIM(Cli.apecli)
          END + '  (' + LTRIM(RTRIM(CAST(Cli.cgccpf AS VARCHAR))) + ')' AS Cliente,
          Ped.codven CodVenSapiens, Rep.usu_iderep CodVen, Rep.aperep Vendedor, Ag.codrep CodAg, Ag.usu_iderep CodAgencia, Ag.aperep NomeFantasiaAgencia,
          CAST(NF.numnfv AS VARCHAR)+'-'+NF.codsnf AS Nota, 
          CASE WHEN NF.codsnf = 'ES'  THEN 'Nota Serviço'
            WHEN NF.codsnf = 'EP'  THEN 'Nota Produto'
            WHEN NF.codsnf = 'NSC' THEN 'Nota Veiculação'
          END AS TipoNota,
          CASE 
            WHEN Ped.tnsser = '90110' AND iNf.codfam = '09001' THEN 'Encarte'
            WHEN Ped.tnsser = '90111' OR iNf.codfam = '08004' THEN 'Class LN'
            WHEN Ped.tnsser = '90113' AND Ped.pedcli like '%PV' THEN 'Embalagem'
            WHEN Ped.tnsser = '90114' THEN 'Embalagem'
            WHEN iNf.codfam = '09002' THEN 'Comercial'
            WHEN iNf.codfam = '09001' THEN 'Publ. Comercial'
            WHEN iNf.codfam = '08001' THEN 'Noticiários'
            WHEN iNf.codfam = '08002' THEN 'Class CM'
            WHEN iNf.codfam = '08003' THEN 'Class Imoveis'
            WHEN iNf.codfam IN ('08007','08010') THEN 'Web'
            WHEN NF.codsnf = 'EP' THEN 'Editorial' 
            WHEN NF.codsnf = 'ES' THEN 'Comercial' 
            ELSE '' 
          END AS TipoServico, 
          NF.vlrfin AS VlrNF, Tcr.numtit as NumTitulo, Tcr.vctpro as DtVencParc, mcr.datpgt as DtPagto,
          Tcr.vlrori as VlrParc, mcr.vlrmov as VlrPago, (Mcr.vlrabe - Mcr.vlrmov) AS VlrEmAberto,
          iPed.percom as PerComisAgencia, FORMAT(NF.datemi, 'MM/yyyy') AS MesAno,
          CASE
            WHEN MONTH(Mcr.datpgt) < 11 AND YEAR(Mcr.datpgt) <= 2024 AND Rep.usu_iderep = '1468' THEN 3
            ELSE DRA.percom
          END AS PerComisVend,
          CAST(
            ((mcr.vlrmov) - ((mcr.vlrmov) * (iPed.percom / 100))) * 
            (
              CASE 
                WHEN MONTH(mcr.datpgt) <= 11 AND YEAR(mcr.datpgt) <= 2024 AND Rep.usu_iderep = '1468' 
                THEN 3 -- Substitua por outro valor conforme necessário
                ELSE DRA.percom 
              END / 100
            ) AS DECIMAL(10,2)
          ) AS VlrComis
      ";
    $querServio =
      " FROM e140nfv NF
          INNER JOIN e140isv iNf	WITH (NOLOCK) ON (NF.codemp = iNf.codemp AND NF.codfil = iNf.codfil AND NF.codsnf = iNf.codsnf AND NF.numnfv = iNf.numnfv)
          INNER JOIN e120isp iPed	WITH (NOLOCK) ON (iNf.filped = iPed.codfil AND iNf.numped = iPed.numped AND iNf.seqisp = iPed.seqisp)
          INNER JOIN e120ped Ped	WITH (NOLOCK) ON (iPed.codemp = Ped.codemp  AND iPed.codfil = Ped.codfil  AND iPed.numped = Ped.numped)
          INNER JOIN e301tcr Tcr	WITH (NOLOCK) ON (NF.codemp = Tcr.codemp	 AND NF.codfil = Tcr.codfil	AND NF.numnfv = Tcr.numnfv   AND NF.codsnf = Tcr.codsnf)
          INNER JOIN e301mcr Mcr	WITH (NOLOCK) ON (Tcr.codemp	= Mcr.codemp AND Tcr.codfil  = Mcr.codfil	AND Tcr.numtit  = Mcr.numtit AND Tcr.codtpt	 = Mcr.codtpt)
          INNER JOIN e090rep Rep	WITH (NOLOCK) ON (Ped.codven = Rep.codrep)
          INNER JOIN e090rep Ag	  WITH (NOLOCK) ON (Ped.codrep = Ag.codrep)
          INNER JOIN e090hrp DRA	WITH (NOLOCK) ON (Rep.codrep = DRA.codrep)
          INNER JOIN e085cli Cli	WITH (NOLOCK) ON (NF.codcli  = Cli.codcli)
     ";

    $queryProduto =
      " FROM e140nfv NF
          INNER JOIN e140ipv INf	WITH (NOLOCK) ON (NF.codemp = iNf.codemp AND NF.codfil = iNf.codfil AND NF.codsnf = iNf.codsnf AND NF.numnfv = iNf.numnfv)
          INNER JOIN e120ipd iPed	WITH (NOLOCK) ON (NF.codemp = iPed.codemp AND NF.codfil = iPed.codfil AND iNf.numped = iPed.numped AND iPed.seqipd = iNf.seqipd)
          INNER JOIN e120ped Ped	WITH (NOLOCK) ON (iPed.codemp = Ped.codemp  AND iPed.codfil = Ped.codfil  AND iPed.numped = Ped.numped)
          INNER JOIN e301tcr Tcr	WITH (NOLOCK) ON (NF.codemp = Tcr.codemp	 AND NF.codfil = Tcr.codfil	AND NF.numnfv = Tcr.numnfv   AND NF.codsnf = Tcr.codsnf)
          INNER JOIN e301mcr mcr	WITH (NOLOCK) ON (Tcr.codemp	= Mcr.codemp AND Tcr.codfil  = Mcr.codfil	AND Tcr.numtit  = Mcr.numtit AND Tcr.codtpt	 = Mcr.codtpt)
          INNER JOIN e090rep Rep	WITH (NOLOCK) ON (Ped.codven = Rep.codrep)
          INNER JOIN e090rep Ag	  WITH (NOLOCK) ON (Ped.codrep = Ag.codrep)
          INNER JOIN e090hrp DRA	WITH (NOLOCK) ON (Rep.codrep = DRA.codrep)
          INNER JOIN e085cli Cli	WITH (NOLOCK) ON (NF.codcli  = Cli.codcli)
      ";

    // Condição
    $whereServico =
      "WHERE NF.sitnfv = '2'
        AND Ped.tnsser = '90113'
        AND	LEFT(Ped.pedcli,2) <> 'SR'
        AND	Ped.sitped <> 5 
        AND Tcr.sittit <> 'CA'
        AND	Mcr.datpgt BETWEEN :dtinicio AND :dtfim
      ";

    $whereProduto =
      "WHERE NF.sitnfv = '2'
        AND Ped.tnsser = '90114'
        AND LEFT(Ped.pedcli,2) <> 'SR'
        AND Ped.sitped <> 5 
        AND Tcr.sittit <> 'CA'
        AND	Mcr.datpgt BETWEEN :dtinicio AND :dtfim
      ";

    // Filtro Tipo
    if ($codVen <> 0) {
      $filtro = ")
        SELECT Cliente, CodCli, CpfCnpj, Nota, TipoNota, DtNF, MesAno, NumPedOrc, DtPedido, TipoServico, NumPedido,
          CodVenSapiens, CodVen, Vendedor, CodAg, CodAgencia, NomeFantasiaAgencia, VlrNF, DtVencParc, DtPagto, VlrParc, 
          VlrPago, VlrEmAberto, PerComisAgencia, PerComisVend, VlrComis, NumTitulo, MesAnoPgto
        FROM ComissaoGrafica
        WHERE CodVen = :codVen
        GROUP BY Cliente, CodCli, CpfCnpj, Nota, TipoNota, DtNF, MesAno, NumPedOrc, DtPedido, TipoServico, NumPedido,
          CodVenSapiens, CodVen, Vendedor, CodAg, CodAgencia, NomeFantasiaAgencia, VlrNF, DtVencParc, DtPagto, VlrParc, 
          VlrPago, VlrEmAberto, PerComisAgencia, PerComisVend, VlrComis, NumTitulo, MesAnoPgto
        ORDER BY Cliente, Nota, DtNF, MesAno
      ";
    } else {
      $filtro =
        ")
        SELECT Cliente, CodCli, CpfCnpj, Nota, TipoNota, DtNF, MesAno, NumPedOrc, DtPedido, TipoServico, NumPedido,
          CodVenSapiens, CodVen, Vendedor, CodAg, CodAgencia, NomeFantasiaAgencia, VlrNF, DtVencParc, DtPagto, VlrParc, 
          VlrPago, VlrEmAberto, PerComisAgencia, PerComisVend, VlrComis, NumTitulo, MesAnoPgto
        FROM ComissaoGrafica
        GROUP BY Cliente, CodCli, CpfCnpj, Nota, TipoNota, DtNF, MesAno, NumPedOrc, DtPedido, TipoServico, NumPedido,
          CodVenSapiens, CodVen, Vendedor, CodAg, CodAgencia, NomeFantasiaAgencia, VlrNF, DtVencParc, DtPagto, VlrParc, 
          VlrPago, VlrEmAberto, PerComisAgencia, PerComisVend, VlrComis, NumTitulo, MesAnoPgto
        ORDER BY Cliente, Nota, DtNF, MesAno
      ";
    }

    // Monta o SQL (CTE + PIVOT)
    $sql = " WITH ComissaoGrafica AS ( "
      . "\n " . $queryPadrao  . "\n " . $querServio
      . "\n " . $whereServico . "\n UNION ALL"
      . "\n " . $queryPadrao  . "\n " . $queryProduto
      . "\n " . $whereProduto . "\n " . $filtro;

    // echo "<pre>";
    // var_dump($sql);
    // die();

    // 4) executa e devolve
    $stmt = $this->senior->prepare($sql);
    $stmt->execute([
      ':dtinicio' => $dtInicio,
      ':dtfim'    => $dtFim,
      ':codVen'   => $codVen,
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
