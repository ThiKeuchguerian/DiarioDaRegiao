<?php
require_once __DIR__ . '/../DBConnect.php';

class ContabilApuracaoNfEntrada
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
    // echo "<pre>";
    // var_dump($primeiro, $ultimo);
    // die();
    return [$primeiro, $ultimo];
  }

  /**
   * Método público que dispara todas as consultas e retorna um array com cada resultado
   */
  public function gerarRelatorio(string $mesAno): array
  {
    list($primeiroDia, $ultimoDia) = self::obterPrimeiroUltimoDia($mesAno);

    return [
      'notasEntrada' => $this->ConsultaNotasEntrada($primeiroDia, $ultimoDia),
      'loteContabil' => $this->ConsultaLoteContabil($primeiroDia, $ultimoDia),
    ];
  }

  private function ConsultaNotasEntrada(string $primeiroDia, string $ultimoDia): array
  {
    $sql = "SELECT Nfc.CODEMP, Nfc.CODFIL AS NFC_CODFIL, Nfc.CODFOR AS NFC_CODFOR, Nfc.NUMNFC AS NFC_NUMNFC, Nfc.CODSNF, Nfc.TIPNFE, 
        Nfc.CODEDC, Nfc.CODTRI, Nfc.DATENT AS NFC_DATENT, Nfc.TNSPRO, Nfc.TNSSER, Nfc.NOPPRO, Nfc.NOPSER, Nfc.DATEMI AS NFC_DATEMI, 
        Nfc.UFSCIC, Nfc.CODCPG, Nfc.CODFPG, Nfc.CODMOE, Nfc.DATMOE, Nfc.COTMOE, Nfc.FECMOE, Nfc.CODFCR, Nfc.DATFCR, Nfc.CODTRA, 
        Nfc.CODRED, Nfc.QTDEMB, Nfc.CODEMB, Nfc.NUMEMB, Nfc.CODMS1, Nfc.CODMS2, Nfc.CODMS3, Nfc.CODMS4, Nfc.OBSNFC, Nfc.PESBRU, 
        Nfc.PESLIQ, Nfc.PERDS1, Nfc.PERDS2, Nfc.PERFIN, Nfc.VLRDZF, Nfc.VLRFRE, Nfc.CIFFOB, Nfc.VLRSEG, Nfc.VLREMB, Nfc.VLRENC, 
        Nfc.VLROUT, Nfc.VLRDAR, Nfc.VLRFRD, Nfc.VLROUD, Nfc.VLRBPR, Nfc.VLRDPR, Nfc.VLRBSE, Nfc.VLRDSE, Nfc.VLRDS1, Nfc.VLRDS2, 
        Nfc.VLRBFU, Nfc.VLRFUN, Nfc.VLRBIP, Nfc.QTDBIP, Nfc.VLRIPI, Nfc.VLRBID, Nfc.VLRIPD, Nfc.VLRBIC, Nfc.VLRICM, Nfc.VLRBSI, 
        Nfc.VLRSIC, Nfc.VLRBSD, Nfc.VLRISD, Nfc.VLRBSP, Nfc.VLRSTP, Nfc.VLRBSC, Nfc.VLRSTC, Nfc.VLRBIS, Nfc.VLRISS, Nfc.VLRBIR, 
        Nfc.VLRIRF, Nfc.VLRBIN, Nfc.VLRINS, Nfc.VLRLPR, Nfc.VLRLSE, Nfc.VLRLOU, Nfc.VLRLIQ, Nfc.VLRINF, Nfc.VLRFIN, Nfc.SITNFC, 
        Nfc.CODMOT, Nfc.VERCAL, Nfc.INTIMP, Nfc.NUMLOT, Nfc.FORISS, Nfc.INDSIG, Nfc.USUGER, Nfc.DATGER, Nfc.HORGER, Nfc.PERFRE, 
        Nfc.PERSEG, Nfc.PEREMB, Nfc.PERENC, Nfc.PEROUT, Nfc.SEQORM, Nfc.VLRBPI, Nfc.QTDBPI, Nfc.VLRPIS, Nfc.EXPWMS, Nfc.INDSIN, 
        Nfc.PRCNFC, Nfc.VLRBCR, Nfc.QTDBCO, Nfc.VLRCOR, Nfc.VLRBCL, Nfc.VLRCSL, Nfc.VLRBPT, Nfc.VLRPIT, Nfc.VLRBCT, Nfc.VLRCRT,
        Nfc.VLRBOR, Nfc.VLROUR, Nfc.VLRBII, Nfc.VLRIIM, Nfc.NUMDOI, Nfc.DATDOI, Nfc.INTPAT, Nfc.VLRRIS, Nfc.VLROCL, Nfc.VLROPT, 
        Nfc.VLROCT, Nfc.VLROOR, Nfc.CODSEL, Nfc.CODSSL, Nfc.PERDS3, Nfc.PERDS4, Nfc.PERDS5, Nfc.VLRDS3, Nfc.VLRDS4, Nfc.VLRDS5, 
        Nfc.BECIPI, Nfc.VECIPI, Nfc.BECICM, Nfc.VECICM, Nfc.VLRBIE, Nfc.VLRIEM, Nfc.VLRFEI, Nfc.VLRSEI, Nfc.VLROUI, Nfc.BCOIMP, 
        Nfc.COFIMP, Nfc.BPIIMP, Nfc.PISIMP, Nfc.NUMCNT, Nfc.IDENFC, Nfc.NUMCTR, Nfc.ROTNAP, Nfc.FILAPR, Nfc.NUMAPR, Nfc.SITAPR,
        Nfc.PERICF, Nfc.ICMFRE, Nfc.CLIRCB, Nfc.VLRBPF, Nfc.VLRPIF, Nfc.QTDBPF, Nfc.VLRBCF, Nfc.VLRCFF, Nfc.QTDBCF, Nfc.ROTANX, 
        Nfc.NUMANX, Nfc.PLAVEI, Nfc.CODVIA, Nfc.CHVNEL, Nfc.SOMFRE, Nfc.UFSVEI, Nfc.NUMINT, Nfc.FILFIX, Nfc.NUMFIX, Nfc.FILOCP, 
        Nfc.NUMOCP, Nfc.CODEQU, Nfc.NUMCFI, Nfc.TIPNDI, Nfc.LOCDES, Nfc.DATDES, Nfc.UFSDES, Nfc.CODEXP, Nfc.NUMDFS, Forn.SIGUFS, 
        Forn.CODGRE, Cpg.DESCPG, Nfc.VLRSUB, Nfc.TOTCIT, Nfc.VLRIMP, Nfc.USUFEC, Nfc.DATFEC, Nfc.HORFEC, Nfc.VLRBSN, Nfc.VLRSEN, 
        Nfc.TIPCTE, Nfc.TIPSER, Nfc.SEQENT, Nfc.QECIPI, Nfc.TIPRAF, Nfc.VLRIOR, Nfc.VLRBDE, Nfc.VLRIDE, Nfc.BASFCP, Nfc.VLRFCP, 
        Nfc.BSTFCP, Nfc.VSTFCP, Nfc.BREFCP, Nfc.VREFCP, Nfc.ICMBFC, Nfc.ICMVFC, Nfc.VERDOC, Nfc.RAIREM, Nfc.RAIDES, Nfg.VDIFCS, 
        Nfg.EFIFCS, Nfg.VICSDT, Nfc.VLRICD, Nfg.QTMBIC, Nfg.VMOICM, Nfg.QTMBIR, Nfg.VMOICR, Nfg.QTMBIF, Nfg.VMOICF, Nfg.QTMBID, 
        Nfg.VMOICD 
      FROM E440NFC Nfc
        INNER JOIN E095FOR Forn ON Nfc.CODFOR = Forn.CODFOR 
        INNER JOIN E028CPG Cpg ON Cpg.CODEMP = Nfc.CODEMP AND Cpg.CODCPG = Nfc.CODCPG 
        LEFT JOIN E440NFG Nfg  ON Nfg.CODEMP = Nfc.CODEMP AND Nfg.CODFIL = Nfc.CODFIL AND Nfg.CODFOR = Nfc.CODFOR AND Nfg.CODSNF = Nfc.CODSNF AND Nfg.NUMNFC = Nfc.NUMNFC 
      WHERE Nfc.CODEMP = 1  AND Nfc.TIPNFE NOT IN (9, 10)  AND 0=0 AND ( (Nfc.CODFIL = 1) )  AND (Nfc.DATENT BETWEEN :primeiro AND :ultimo) AND
      (Nfc.TNSPRO NOT IN (SELECT E099UXT.CODTNS FROM E099UXT, E001TNS WHERE E099UXT.CODEMP = 1 AND E099UXT.CODEMP = E001TNS.CODEMP AND E099UXT.CODUSU = 1 AND E099UXT.CODTNS = E001TNS.CODTNS)) AND 
      (Nfc.TNSSER NOT IN (SELECT E099UXT.CODTNS FROM E099UXT, E001TNS WHERE E099UXT.CODEMP = 1  AND E099UXT.CODEMP = E001TNS.CODEMP AND E099UXT.CODUSU = 1 AND E099UXT.CODTNS = E001TNS.CODTNS))
      UNION 
      SELECT DISTINCT Nfc.CODEMP, Nfc.CODFIL AS NFC_CODFIL, Nfc.CODFOR AS NFC_CODFOR, Nfc.NUMNFC AS NFC_NUMNFC, Nfc.CODSNF, Nfc.TIPNFE, Nfc.CODEDC,
        Nfc.CODTRI, Nfc.DATENT AS NFC_DATENT, Nfc.TNSPRO, Nfc.TNSSER, Nfc.NOPPRO, Nfc.NOPSER, Nfc.DATEMI AS NFC_DATEMI, Nfc.UFSCIC, Nfc.CODCPG, 
        Nfc.CODFPG, Nfc.CODMOE, Nfc.DATMOE, Nfc.COTMOE, Nfc.FECMOE, Nfc.CODFCR, Nfc.DATFCR, Nfc.CODTRA, Nfc.CODRED, Nfc.QTDEMB, 
        Nfc.CODEMB, Nfc.NUMEMB, Nfc.CODMS1, Nfc.CODMS2, Nfc.CODMS3, Nfc.CODMS4, Nfc.OBSNFC, Nfc.PESBRU, Nfc.PESLIQ, Nfc.PERDS1, 
        Nfc.PERDS2, Nfc.PERFIN, Nfc.VLRDZF, Nfc.VLRFRE, Nfc.CIFFOB, Nfc.VLRSEG, Nfc.VLREMB, Nfc.VLRENC, Nfc.VLROUT, Nfc.VLRDAR, 
        Nfc.VLRFRD, Nfc.VLROUD, Nfc.VLRBPR, Nfc.VLRDPR, Nfc.VLRBSE, Nfc.VLRDSE, Nfc.VLRDS1, Nfc.VLRDS2, Nfc.VLRBFU, Nfc.VLRFUN, 
        Nfc.VLRBIP, Nfc.QTDBIP, Nfc.VLRIPI, Nfc.VLRBID, Nfc.VLRIPD, Nfc.VLRBIC, Nfc.VLRICM, Nfc.VLRBSI, Nfc.VLRSIC, Nfc.VLRBSD, 
        Nfc.VLRISD, Nfc.VLRBSP, Nfc.VLRSTP, Nfc.VLRBSC, Nfc.VLRSTC, Nfc.VLRBIS, Nfc.VLRISS, Nfc.VLRBIR, Nfc.VLRIRF, Nfc.VLRBIN, 
        Nfc.VLRINS, Nfc.VLRLPR, Nfc.VLRLSE, Nfc.VLRLOU, Nfc.VLRLIQ, Nfc.VLRINF, Nfc.VLRFIN, Nfc.SITNFC, Nfc.CODMOT, Nfc.VERCAL,
        Nfc.INTIMP, Nfc.NUMLOT, Nfc.FORISS, Nfc.INDSIG, Nfc.USUGER, Nfc.DATGER, Nfc.HORGER, Nfc.PERFRE, Nfc.PERSEG, Nfc.PEREMB, 
        Nfc.PERENC, Nfc.PEROUT, Nfc.SEQORM, Nfc.VLRBPI, Nfc.QTDBPI, Nfc.VLRPIS, Nfc.EXPWMS, Nfc.INDSIN, Nfc.PRCNFC, Nfc.VLRBCR, 
        Nfc.QTDBCO, Nfc.VLRCOR, Nfc.VLRBCL, Nfc.VLRCSL, Nfc.VLRBPT, Nfc.VLRPIT, Nfc.VLRBCT, Nfc.VLRCRT, Nfc.VLRBOR, Nfc.VLROUR, 
        Nfc.VLRBII, Nfc.VLRIIM, Nfc.NUMDOI, Nfc.DATDOI, Nfc.INTPAT, Nfc.VLRRIS, Nfc.VLROCL, Nfc.VLROPT, Nfc.VLROCT, Nfc.VLROOR, 
        Nfc.CODSEL, Nfc.CODSSL, Nfc.PERDS3, Nfc.PERDS4, Nfc.PERDS5, Nfc.VLRDS3, Nfc.VLRDS4, Nfc.VLRDS5, Nfc.BECIPI, Nfc.VECIPI, 
        Nfc.BECICM, Nfc.VECICM, Nfc.VLRBIE, Nfc.VLRIEM, Nfc.VLRFEI, Nfc.VLRSEI, Nfc.VLROUI, Nfc.BCOIMP, Nfc.COFIMP, Nfc.BPIIMP, 
        Nfc.PISIMP, Nfc.NUMCNT, Nfc.IDENFC, Nfc.NUMCTR, Nfc.ROTNAP, Nfc.FILAPR, Nfc.NUMAPR, Nfc.SITAPR, Nfc.PERICF, Nfc.ICMFRE, 
        Nfc.CLIRCB, Nfc.VLRBPF, Nfc.VLRPIF, Nfc.QTDBPF, Nfc.VLRBCF, Nfc.VLRCFF, Nfc.QTDBCF, Nfc.ROTANX, Nfc.NUMANX, Nfc.PLAVEI, 
        Nfc.CODVIA, Nfc.CHVNEL, Nfc.SOMFRE, Nfc.UFSVEI, Nfc.NUMINT, Nfc.FILFIX, Nfc.NUMFIX, Nfc.FILOCP, Nfc.NUMOCP, Nfc.CODEQU, 
        Nfc.NUMCFI, Nfc.TIPNDI, Nfc.LOCDES, Nfc.DATDES, Nfc.UFSDES, Nfc.CODEXP, Nfc.NUMDFS, Forn.SIGUFS, Forn.CODGRE, Cpg.DESCPG, 
        Nfc.VLRSUB, Nfc.TOTCIT, Nfc.VLRIMP, Nfc.USUFEC, Nfc.DATFEC, Nfc.HORFEC, Nfc.VLRBSN, Nfc.VLRSEN, Nfc.TIPCTE, Nfc.TIPSER, 
        Nfc.SEQENT, Nfc.QECIPI, Nfc.TIPRAF, Nfc.VLRIOR, Nfc.VLRBDE, Nfc.VLRIDE, Nfc.BASFCP, Nfc.VLRFCP, Nfc.BSTFCP, Nfc.VSTFCP, 
        Nfc.BREFCP, Nfc.VREFCP, Nfc.ICMBFC, Nfc.ICMVFC, Nfc.VERDOC, Nfc.RAIREM, Nfc.RAIDES, Nfg.VDIFCS, Nfg.EFIFCS, Nfg.VICSDT, 
        Nfc.VLRICD, Nfg.QTMBIC, Nfg.VMOICM, Nfg.QTMBIR, Nfg.VMOICR, Nfg.QTMBIF, Nfg.VMOICF, Nfg.QTMBID, Nfg.VMOICD 
      FROM E440NFC Nfc
        INNER JOIN E095FOR Forn ON Forn.CODFOR = Nfc.CODFOR 
        INNER JOIN E028CPG Cpg ON Cpg.CODEMP = Nfc.CODEMP AND Cpg.CODCPG = Nfc.CODCPG 
        LEFT JOIN E440NFG Nfg  ON Nfg.CODEMP = Nfc.CODEMP AND Nfg.CODFIL = Nfc.CODFIL AND Nfg.CODFOR = Nfc.CODFOR AND Nfg.CODSNF = Nfc.CODSNF AND Nfg.NUMNFC = Nfc.NUMNFC 
      WHERE Nfc.CODEMP = 1 AND Nfc.TIPNFE NOT IN (9, 10)  AND 0=0 AND ( (Nfc.CODFIL = 1) ) AND (Nfc.DATENT BETWEEN :primeiro AND :ultimo) AND
      (Nfc.TNSPRO NOT IN (SELECT E099UXT.CODTNS FROM E099UXT, E001TNS WHERE E099UXT.CODEMP = 1  AND E099UXT.CODEMP = E001TNS.CODEMP AND E099UXT.CODUSU = 1 AND E099UXT.CODTNS = E001TNS.CODTNS)) AND 
      (Nfc.TNSSER NOT IN (SELECT E099UXT.CODTNS FROM E099UXT, E001TNS WHERE E099UXT.CODEMP = 1  AND E099UXT.CODEMP = E001TNS.CODEMP AND E099UXT.CODUSU = 1 AND E099UXT.CODTNS = E001TNS.CODTNS)) 
      ORDER BY NFC_DATENT, Nfc.NUMNFC
    ";
    $stmt = $this->senior->prepare($sql);
    // echo "<pre>";
    // var_dump($stmt, $primeiroDia, $ultimoDia);
    // die();
    $stmt->execute([':primeiro' => $primeiroDia, ':ultimo' => $ultimoDia]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  private function ConsultaLoteContabil(string $primeiroDia, string $ultimoDia): array
  {
    $sql = " SELECT E640LOT.CODEMP, E640LOT.NUMLOT, E640LOT.TIPLCT, E640LOT.ORILCT, E640LOT.CODFIL, E640LOT.DATLOT, E640LOT.DATFIX, E640LOT.DESLOT, E640LOT.TOTDEB, 
        E640LOT.TOTCRE, E640LOT.TOTINF, E640LOT.TOTLCT, E640LOT.USULOT, E640LOT.CODUSU, E640LOT.DATENT, E640LOT.HORENT, E640LOT.SITLOT, E640LOT.LOTSIN,
        (SELECT SUM(E640LCT.VLRLCT) FROM E640LCT WHERE E640LCT.CTADEB <> 0 AND E640LCT.SITLCT IN (1,2) AND E640LOT.CODEMP = E640LCT.CODEMP AND E640LOT.NUMLOT = E640LCT.NUMLOT) TOTDEBLCT, 
        (SELECT SUM(E640LCT.VLRLCT) FROM E640LCT WHERE E640LCT.CTACRE <> 0 AND E640LCT.SITLCT IN (1,2) AND E640LOT.CODEMP = E640LCT.CODEMP AND E640LOT.NUMLOT = E640LCT.NUMLOT ) TOTCRELCT 
      FROM E640LOT 
      WHERE E640LOT.CODEMP = 1 AND E640LOT.DATLOT >= :primeiro  AND E640LOT.DATLOT <= :ultimo  AND E640LOT.SITLOT = 2  AND 0=0  AND E640LOT.ORILCT = 'CPR' 
      ORDER BY E640LOT.DATLOT
    ";

    $stmt = $this->senior->prepare($sql);
    // echo "<pre>";
    // var_dump($stmt, $numNF);
    // die();
    $stmt->execute([':primeiro' => $primeiroDia, ':ultimo' => $ultimoDia]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }
}