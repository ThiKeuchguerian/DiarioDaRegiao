<?php
require_once __DIR__ . '/../DBConnect.php';

class GraficaCheckMetas
{
  private $senior;

  public function __construct()
  {
    $this->senior = DatabaseConnection::getConnection('senior');

    $this->senior->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
  }

  /**
   * Retorna o relatório PIVOT das metas gráficas para o ano e tipo
   *
   * @param string $ano      ex: "2025"
   * @param int    $tipo     1 = Com­ercial, 2 = Embalagem
   * @return array           linhas já formatadas
   * @throws InvalidArgumentException
   */
  public function consultaMetas(string $ano, int $tipo): array
  {
    // Converte tipo para string (0 = ambos)
    $mapTipo = [
      0 => ['Comercial', 'Embalagem'],
      1 => ['Comercial'],
      2 => ['Embalagem'],
    ];

    $tipoServ = "'" . implode("','", $mapTipo[$tipo]) . "'";

    // echo "<pre>";
    // var_dump($tipoServ);
    // die();

    // Datas unívocas YYYYMMDD
    $inicio = $ano . '0101';
    $fim    = $ano . '1231';

    $queryPadrao =
      "SELECT Ped.pedcli NumPedOrc, Ped.numped AS NumPedido, NF.codcli AS CodCli, Ped.datemi AS DtPedido,  NF.datemi AS DtNF,
        Cli.cgccpf AS CpfCnpj,
        CASE 
          WHEN LTRIM(RTRIM(ISNULL(Cli.apecli, ''))) = '' THEN RTRIM(Cli.nomcli)
          ELSE RTRIM(Cli.apecli)
        END + '  (' + LTRIM(RTRIM(CAST(Cli.cgccpf AS VARCHAR))) + ')' AS Cliente,
        CASE 
          WHEN Ped.codven = '' THEN Ped.codcrt 
          ELSE Ped.codven
        END AS CodVenSapiens, 
        CASE 
          WHEN Rep.usu_iderep IS NULL OR Rep.usu_iderep = NULL THEN '4'
          ELSE Rep.usu_iderep 
        END AS CodVen, 
          CASE 
              WHEN Rep.aperep IS NULL OR Rep.aperep = NULL THEN 'ADM COM'
              ELSE Rep.aperep
          END AS Vendedor, Ag.codrep CodAg, Ag.usu_iderep CodAgencia, Ag.aperep NomeFantasiaAgencia,
        FORMAT(NF.datemi, 'MM/yyyy') AS MesAno,
          CAST(NF.numnfv AS VARCHAR)+'-'+NF.codsnf AS Nota, 
          CASE WHEN NF.codsnf = 'ES'  THEN 'Nota Serviço'
              WHEN NF.codsnf = 'EP'  THEN 'Nota Produto'
              WHEN NF.codsnf = 'NSC' THEN 'Nota Veiculação'
          END AS TipoNota,
          CASE 
              WHEN ped.tnsser = '90110' AND INf.codfam = '09001' THEN 'Encarte'
              WHEN ped.tnsser = '90111' OR INf.codfam = '08004' THEN 'Class LN'
              WHEN ped.tnsser = '90113' AND ped.pedcli like '%PV' THEN 'Embalagem'
              WHEN ped.tnsser = '90114' THEN 'Embalagem'
              WHEN INf.codfam = '09002' THEN 'Comercial'
              WHEN INf.codfam = '09001' THEN 'Publ. Comercial'
              WHEN INf.codfam = '08001' THEN 'Noticiários'
              WHEN INf.codfam = '08002' THEN 'Class CM'
              WHEN INf.codfam = '08003' THEN 'Class Imoveis'
              WHEN INf.codfam IN ('08007','08010') THEN 'Web'
              WHEN NF.codsnf = 'EP' THEN 'Editorial' 
              WHEN NF.codsnf = 'ES' THEN 'Comercial' 
          ELSE '' 
          END AS TipoServico, ped.tnsser, ped.tnspro, INf.qtdfat AS QtdeVenda,
        CAST((NF.vlrfin / t.TotalItens) AS REAL) AS VlrNF, (NF.vlrliq / t.TotalItens) AS VlrLiqNota,
      ";
    $querServio =
      " INf.tnsser AS TSerPro, INf.codser AS CodProduto, INf.cplisv AS DescProduto
        FROM e140nfv NF
          INNER JOIN e140isv INf	WITH (NOLOCK) ON (NF.codemp = 1 AND NF.codfil = INf.codfil AND NF.codsnf = INf.codsnf AND NF.numnfv = INf.numnfv)
          INNER JOIN e120isp iPed	WITH (NOLOCK) ON (INf.filped = iPed.codfil AND INf.numped = iPed.numped AND INf.seqisp = iPed.seqisp)
          INNER JOIN e120ped Ped	WITH (NOLOCK) ON (iPed.codemp = Ped.codemp  AND iPed.codfil = Ped.codfil  AND iPed.numped = Ped.numped)
          LEFT  JOIN e090rep Rep	WITH (NOLOCK) ON (Ped.codven = Rep.codrep)
          LEFT  JOIN e090rep Ag	WITH (NOLOCK) ON (Ped.codrep = Ag.codrep)
          LEFT  JOIN e090hrp DRA	WITH (NOLOCK) ON (Rep.codrep = DRA.codrep)
          INNER JOIN e085cli Cli	WITH (NOLOCK) ON (NF.codcli  = Cli.codcli)
          CROSS APPLY (SELECT COUNT(*) AS TotalItens FROM e140isv x WHERE x.codemp = NF.codemp AND x.codfil = NF.codfil AND x.codsnf = NF.codsnf AND x.numnfv = NF.numnfv ) t
     ";

    $queryProduto =
      " INf.tnspro AS TSerPro, INf.codpro AS CodProduto, INf.cplipv AS DescProduto
        FROM e140nfv NF
          INNER JOIN e140ipv INf	WITH (NOLOCK) ON (NF.codemp = INf.codemp AND NF.codfil = INf.codfil AND NF.codsnf = INf.codsnf AND NF.numnfv = INf.numnfv)
          INNER JOIN e120ipd Iped	WITH (NOLOCK) ON (NF.codemp = Iped.codemp AND NF.codfil = Iped.codfil AND INf.numped = Iped.numped AND Iped.seqipd = INf.seqipd)
          INNER JOIN e120ped Ped	WITH (NOLOCK) ON (Iped.codemp = Ped.codemp  AND Iped.codfil = Ped.codfil  AND Iped.numped = Ped.numped)
          LEFT  JOIN e090rep Rep	WITH (NOLOCK) ON (Ped.codven = Rep.codrep)
          LEFT  JOIN e090rep Ag	WITH (NOLOCK) ON (Ped.codrep = Ag.codrep)
          LEFT  JOIN e090hrp DRA	WITH (NOLOCK) ON (Rep.codrep = DRA.codrep)
          INNER JOIN e085cli Cli	WITH (NOLOCK) ON (NF.codcli  = Cli.codcli)
          CROSS APPLY (SELECT COUNT(*) AS TotalItens FROM e140ipv x WHERE x.codemp = NF.codemp AND x.codfil = NF.codfil AND x.codsnf = NF.codsnf AND x.numnfv = NF.numnfv ) t
      ";

    // Condição
    $whereServico =
      "WHERE NF.datemi BETWEEN :inicio AND :fim
          AND ped.tnsser IN ('90110','90111','90113','90114')
          AND LEFT(Ped.pedcli,2) <> 'SR'
          AND Ped.sitped   <> '5'
          AND NF.sitnfv     = '2'
      ";

    $whereProduto =
      "WHERE NF.datemi BETWEEN :inicio AND :fim
        AND LEFT(Ped.pedcli,2) <> 'SR'
        AND Ped.sitped   <> '5'
        AND NF.sitnfv     = '2'
      ";

    // Filtro Tipo
    $filtro =
      ")
        SELECT Cliente, CodCli, CpfCnpj, Nota, TipoNota, DtNF, MesAno, NumPedOrc, DtPedido, TipoServico, NumPedido,
          CodVenSapiens, CodVen, Vendedor, CodAg, CodAgencia, NomeFantasiaAgencia, CodProduto, DescProduto, QtdeVenda, VlrNF
        FROM CheckMetasGrafica
        WHERE TipoServico IN ($tipoServ)
        GROUP BY Cliente, CodCli, CpfCnpj, Nota, TipoNota, DtNF, MesAno, NumPedOrc, DtPedido, TipoServico, NumPedido,
          CodVenSapiens, CodVen, Vendedor, CodAg, CodAgencia, NomeFantasiaAgencia, CodProduto, DescProduto, QtdeVenda, VlrNF
        ORDER BY Cliente, Nota, DtNF, MesAno
      ";

    // Monta o SQL (CTE + PIVOT)
    $sql = " WITH CheckMetasGrafica AS ( "
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
      ':inicio' => $inicio,
      ':fim'    => $fim,
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
