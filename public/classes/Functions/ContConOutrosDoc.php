<?php
require_once __DIR__ . '/../DBConnect.php';

class ContabilConsultaOutrosDoc
{
  private $senior;

  public function __construct()
  {
    $this->senior = DatabaseConnection::getConnection('seniorTeste');

    $this->senior->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
  }

  /**
   * Método público que dispara todas as consultas e retorna um array com cada resultado
   */
  public function gerarRelatorio(string $numNF): array
  {
    return [
      'outrosDocumentos' => $this->consultaOutrosDocumentos($numNF),
      'itens'            => $this->consultaItens($numNF),
    ];
  }

  private function consultaOutrosDocumentos(string $numNF): array
  {
    // Explode e filtra apenas os dígitos
    $parts = array_filter(
      array_map('trim', explode(',', $numNF)),
      fn($v) => ctype_digit($v)
    );

    // Monta params
    $placeholders = [];
    $params       = [];
    foreach ($parts as $i => $nf) {
      $ph = ":numNF{$i}";
      $placeholders[]      = $ph;
      $params[$ph]         = (int)$nf;
    }
    $in = implode(', ', $placeholders);

    $sql = "SELECT Entrada = 'OutrosDoc', O.codcli as CodCli, O.numdoc AS NumDoc, O.seqdoc as Seq, 
      O.codser AS CodSer, O.vlrope as Valor, O.numlot as Lote, O.numnfv as Nota, O.datope AS DtVeiculacao,
      CASE 
        WHEN O.numlot <> '0' THEN 'Sim'
        ELSE 'Não'
      END AS Contabilizado
      FROM E660ODC O
      WHERE O.NUMNFV IN ({$in})
      ORDER BY O.codcli, O.numdoc, O.seqdoc
    ";
    $stmt = $this->senior->prepare($sql);
    // echo "<pre>";
    // var_dump($stmt, $numNF);
    // die();
    // $stmt->bindParam(':numNF', $numNF, PDO::PARAM_STR);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  private function consultaItens(string $numNF): array
  {
    // Explode e filtra apenas os dígitos
    $parts = array_filter(
      array_map('trim', explode(',', $numNF)),
      fn($v) => ctype_digit($v)
    );

    // Monta params
    $placeholders = [];
    $params       = [];
    foreach ($parts as $i => $nf) {
      $ph = ":numNF{$i}";
      $placeholders[]      = $ph;
      $params[$ph]         = (int)$nf;
    }
    $in = implode(', ', $placeholders);

    $sql = " WITH ConsultaItensPedido AS (
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
          AND E140ISV.NUMNFV IN ({$in})
          AND E120PED.TIPPED = 1
          AND E120PED.CODFIL = 1
          AND E120ISP.SITISP = 4
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
        SELECT Entrada = 'ItensNota', CODCLI, NUMPED, SEQISP, CODSER, 
          CAST(ValorVeiculado AS DECIMAL(18, 2)) AS VlrVeiculado, PEDCLI, DATENT, NUMNFV
        FROM AjustandoItensPedido
        ORDER BY CODCLI, NUMNFV, SEQISP
    ";

    $stmt = $this->senior->prepare($sql);
    // echo "<pre>";
    // var_dump($stmt, $numNF);
    // die();
    $stmt->execute($params);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function consultaCliente(string $codCli): array
  {
    // Explode e filtra apenas os dígitos
    $parts = array_filter(
      array_map('trim', explode(',', $codCli)),
      fn($v) => ctype_digit($v)
    );

    // Monta params
    $placeholders = [];
    $params       = [];
    foreach ($parts as $i => $nf) {
      $ph = ":codCli{$i}";
      $placeholders[]      = $ph;
      $params[$ph]         = (int)$nf;
    }
    $in = implode(', ', $placeholders);

    $sql = " SELECT Cli.codcli, Cli.nomcli, Cli.tipcli, Cli.cgccpf, Cli.insest, 
        CONCAT(Cli.endcli, ', ', Cli.nencli) AS Endereco, Cli.baicli, Cli.cidcli, Cli.sigufs
      FROM e085cli Cli
      WHERE codcli IN ({$in})
    ";

    $stmt = $this->senior->prepare($sql);
    // echo "<pre>";
    // var_dump($stmt, $codCli);
    // die();
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function consultaParamCliente(string $codCli): array
  {
    // Explode e filtra apenas os dígitos
    $parts = array_filter(
      array_map('trim', explode(',', $codCli)),
      fn($v) => ctype_digit($v)
    );

    // Monta params
    $placeholders = [];
    $params       = [];
    foreach ($parts as $i => $nf) {
      $ph = ":codCli{$i}";
      $placeholders[]      = $ph;
      $params[$ph]         = (int)$nf;
    }
    $in = implode(', ', $placeholders);

    $sql = " SELECT Cli.codcli, Cli.nomcli, Cli.triicm AS 'T-ICMS', Cli.triipi AS 'T-IPI', Cli.tricof AS 'T-COFINS', Cli.tripis AS 'T-PIS', 
        Cli.retirf AS IR, Cli.retcsl AS CSLL, Cli.retcof AS COFINS, Cli.retpis AS PIS,
        Cli.retour AS 'OutrasR', Cli.retpro AS RetPro
      FROM e085cli Cli
      WHERE codcli IN ({$in})
    ";

    $stmt = $this->senior->prepare($sql);
    // echo "<pre>";
    // var_dump($stmt, $codCli);
    // die();
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
