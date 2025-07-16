<?php
require_once __DIR__ . '/../DBConnect.php';

class RecebimentoContaFinan
{
  private $senior;

  public function __construct()
  {
    $this->senior = DatabaseConnection::getConnection('senior');
    $this->senior->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
  }

  /**
   * Lista produtos de um depósito
   * @param int $codDep
   * @return array
   */
  public function consultaFaturamentoRecebido(array $dados): array
  {
    $ano = $dados['ano'];
    $codEmp = $dados['codEmp'];
    $anoinicio = $ano . '0101';
    $anofim = $ano . '1231';

    $params = [
      ':codEmp' => $codEmp,
      ':anoinicio' => $anoinicio,
      ':anofim' => $anofim
    ];

    $sql  =
      "SELECT DISTINCT tcr.NUMTIT,tcr.CODTPT,tcr.CODCLI,E085CLI.NOMCLI,tcr.VCTPRO,tcr.SITTIT,tcr.CODREP,tcr.CODPOR,tcr.CODCRT,
          tcr.CODTNS,E001TNS.LISMOD,tcr.CODSNF,tcr.NUMNFV,tcr.CODFIL,tcr.NUMCTR,tcr.DATPPT,tcr.VLRORI,tcr.VCTORI,E001TNS.RECDEC,
          E085CLI.CIDCLI,E085CLI.SIGUFS,E085CLI.FONCLI,E085CLI.INTNET,E070FIL.RECVJM,E070FIL.RECVMM,E070FIL.RECVDM,tcr.VLRABE,tcr.PERJRS,tcr.JRSDIA,
          tcr.TIPJRS,tcr.TOLJRS,tcr.PERMUL,tcr.TOLMUL,tcr.DATDSC,tcr.PERDSC,tcr.VLRDSC,E039POR.FLOBAN,E085HCL.MEDATR,tcr.DATCJM,
          E085CLI.CEPINI,E085CLI.INICOB,tcr.TITBAN,tcr.ULTPGT,tcr.NUMARB,tcr.CODCRP,E085CLI.CODGRE,tcr.CODSAC,tcr.CODMOE,tcr.COTEMI,
          tcr.VLRCOM,tcr.VLRBCO,tcr.PERCOM,tcr.COMREC,E001TNS.DESTNS,tcr.CODMPT,tcr.NUMPED,tcr.DATENT,tcr.CODNTG,tcr.CODFRJ,
          tcr.COTFRJ,tcr.CPGNEG,tcr.TAXNEG,tcr.FILCTR,tcr.FILCTR,E070FIN.RECJOA,E070FIN.RECJOD,tcr.FILNFV,tcr.FILNFF,tcr.NUMNFF,
          E085CLI.IDECLI,tcr.NUMECO,tcr.DATNEG,tcr.JRSNEG,tcr.MULNEG,tcr.DSCNEG,tcr.OUTNEG,tcr.CODFPG,tcr.ANTDSC,tcr.CODEMP,
          tcr.PROJRS,tcr.FORNFC,tcr.NUMNFC,tcr.FILNFC,tcr.SNFNFC,tcr.CHEBAN,tcr.CHEAGE,tcr.CHECTA,tcr.CHENUM,tcr.CODBAR,
          tcr.NUMACE,tcr.OBSTCR,E085CLI.CGCCPF,tcr.FILNDB,tcr.NUMNDB,tcr.TPTNDB,E070FIL.RECDPR,tcr.COTNEG,E085HCL.PRZMRT,E090REP.NOMREP,
          tcr.CATTEF,tcr.NSUTEF,tcr.VLRDCA,tcr.VLRDCB,tcr.VLROUD,tcr.SITPEF,E070FIN.RECMOA,tcr.PRDDSC,tcr.NUMDFS,tcr.PEDFRE,
          tcr.PEDNRE,tpc.DATUAC,tpc.CODOCB,tpc.CODACB,tpc.CODFCB,tcr.DSCPON,tcr.JURVEN,E002TPT.RECSOM,tcr.PORANT,tcr.CRTANT,
          tcr.NUMMAL,tcr.CTPNEG,tcr.PARCAR,tcr.NSUHST, 0 AS COTMCR, fpg.TIPFPG ,tcr.ROTANX,tcr.NUMANX,tcr.CTREXT,tcr.CODOPC,
          tcr.LOCTIT,tcr.DATEMI,rat.CTAFIN,c21.DESCTA,fpg.DESFPG,
          (mcr.VLRMOV - mcr.VLRDSC - mcr.VLRIRF - mcr.VLRPIT - mcr.VLRCRT - mcr.VLRCSL + mcr.VLRJRS + mcr.VLRMUL) AS VLRMOV,
          CASE 
            WHEN LEFT(rat.CTAFIN, 4) = 1101 THEN 'Circulação'
            WHEN LEFT(rat.CTAFIN, 4) = 1102 THEN 'Publicidade_Impressa'
            WHEN LEFT(rat.CTAFIN, 4) = 1103 THEN 'Publicidade_Digital'
            WHEN LEFT(rat.CTAFIN, 4) IN (1104, 1105) THEN 'Redes_Social'
            WHEN LEFT(rat.CTAFIN, 4) = 1106 THEN 'Gráfica'
            WHEN LEFT(rat.CTAFIN, 4) IN (1107, 1109, 1202) THEN 'Outras_Receitas'
            ELSE ''
          END AS ContaFin,
          CONCAT(RIGHT('0' + CAST(MONTH(tcr.DATPPT) AS VARCHAR(2)), 2),  '/', YEAR(tcr.DATPPT)) AS MesAno 
        FROM E301TCR tcr
        LEFT JOIN (SELECT CODEMP, CODFIL, NUMTIT, CODTPT, MAX(SEQMOV) AS SEQMOV FROM E301MCR GROUP BY CODEMP, CODFIL, NUMTIT, CODTPT) AS mcr_base 
          ON tcr.CODEMP = mcr_base.CODEMP AND tcr.CODFIL = mcr_base.CODFIL AND tcr.NUMTIT = mcr_base.NUMTIT AND tcr.CODTPT = mcr_base.CODTPT
        LEFT JOIN E301MCR mcr ON mcr.CODEMP = mcr_base.CODEMP AND mcr.CODFIL = mcr_base.CODFIL 
          AND mcr.NUMTIT = mcr_base.NUMTIT AND mcr.CODTPT = mcr_base.CODTPT AND mcr.SEQMOV = mcr_base.SEQMOV
        LEFT JOIN E301TPC tpc ON (tcr.CODEMP = tpc.CODEMP AND tcr.CODFIL = tpc.CODFIL AND tcr.NUMTIT = tpc.NUMTIT AND tcr.CODTPT=tpc.CODTPT)
        LEFT JOIN E066FPG fpg ON (fpg.CODEMP = tcr.CODEMP AND fpg.CODFPG = tcr.CODFPG)
        LEFT JOIN E301RAT rat ON (tcr.CODEMP = rat.CODEMP AND tcr.CODFIL = rat.CODFIL AND tcr.NUMTIT = rat.NUMTIT AND mcr.SEQMOV = rat.SEQMOV)
        LEFT OUTER JOIN e070emp c1 ON rat.codemp = c1.codemp
        LEFT OUTER JOIN e615prj c17 ON rat.codemp = c17.codemp AND rat.numprj = c17.numprj
        LEFT OUTER JOIN e091plf c21 ON rat.codemp = c21.codemp AND rat.ctafin = c21.ctafin
        LEFT OUTER JOIN e045pla c23 ON rat.codemp = c23.codemp AND rat.ctared = c23.ctared, 
          E039POR, E001TNS, E085HCL, E085CLI, E070FIL, E070FIN, E090REP, E002TPT
        WHERE E085HCL.CODCLI = tcr.CODCLI AND E085HCL.CODEMP = tcr.CODEMP AND E085HCL.CODFIL = tcr.CODFIL 
        AND E085CLI.CODCLI = tcr.CODCLI AND E090REP.CODREP = tcr.CODREP AND E070FIL.CODEMP = tcr.CODEMP AND E070FIL.CODFIL = tcr.CODFIL
        AND E070FIN.CODEMP = E070FIL.CODEMP AND E070FIN.CODFIL = E070FIL.CODFIL AND E039POR.CODPOR = tcr.CODPOR AND E039POR.CODEMP = tcr.CODEMP
        AND E001TNS.CODEMP = tcr.CODEMP AND E001TNS.CODTNS = tcr.CODTNS AND E002TPT.CODTPT = tcr.CODTPT 
        AND E001TNS.LISMOD = 'CRE' AND tcr.CODFIL = 1
        AND tcr.CODTPT NOT IN ('ADC')
        AND tcr.SITTIT IN ('LC','LI','LO','LP','LQ','LV')
      ";

    if (!empty($codEmp)) {
      $sql .= "AND tcr.CODEMP = :codEmp ";
      $params[':codEmp'] = $codEmp;
    }

    if (!empty($ano)) {
      $sql .= "AND (mcr.DATPGT BETWEEN :anoinicio AND :anofim) ";
      $params[':anoinicio'] = $anoinicio;
      $params[':anofim']    = $anofim;
    }
    $sql .= "\n ORDER BY  MesAno, ContaFin, DATPPT";
    // depurar($sql, $params);
    $stmt = $this->senior->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
