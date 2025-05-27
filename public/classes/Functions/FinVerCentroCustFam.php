<?php

require_once __DIR__ . '/../DBConnect.php';

class CentroCustoFam
{
  private $senior;

  public function __construct()
  {
    $this->senior = DatabaseConnection::getConnection('senior');
  }

  // -----------------------------------------------------------
  // SELECTS
  // -----------------------------------------------------------
  public function consultaFamilia(): array
  {
    $sql = 
    " SELECT f.codfam, f.desfam FROM e012fam f 
        INNER JOIN e120isp p WITH (NOLOCK) ON f.codemp = p.codemp AND f.codfam = p.codfam
        WHERE f.codemp = 1 
        GROUP BY f.codfam, f.desfam
      UNION ALL
      SELECT f.codfam, f.desfam FROM e012fam f
        INNER JOIN e120ipd p WITH (NOLOCK) ON f.codemp = p.codemp and f.codfam = p.codfam
        WHERE f.codemp = 1 
        GROUP BY f.codfam, f.desfam
      ORDER BY f.codfam, f.desfam
    ";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  }

  public function consultaPedido(string $CodFam): array
  {
    $querySer = "SELECT ped.numped, isp.pedcli, ped.codcli, cli.nomcli, 
        ped.datemi, isp.datent, ped.tnsser, isp.codfam, isp.codser, 
        ser.desser, rat.ctafin, rat.ctared, rat.codccu
      from e120ped ped
      inner join e120isp isp with (nolock) on ped.codemp = isp.codemp and ped.codfil = isp.codfil and ped.numped = isp.numped
      inner join e120rat rat with (nolock) on rat.codemp = isp.codemp and rat.codfil = isp.codfil and rat.numped = isp.numped and rat.tnsser = isp.tnsser and rat.seqisp = isp.seqisp
      left outer join e085cli cli with (nolock) on ped.codcli = cli.codcli
      left outer join e080ser ser with (nolock) on isp.codser = ser.codser
    ";

    $queryProd = "SELECT  ped.numped, isp.pedcli, ped.codcli, cli.nomcli, 
        ped.datemi, isp.datent, ped.tnsser, isp.codfam, isp.codpro, 
        ser.despro, rat.ctafin, rat.ctared, rat.codccu
      from e120ped ped
      inner join e120ipd isp with (nolock) on ped.codemp = isp.codemp and ped.codfil = isp.codfil and ped.numped = isp.numped
      inner join e120rat rat with (nolock) on rat.codemp = isp.codemp and rat.codfil = isp.codfil and rat.numped = isp.numped and rat.tnspro = isp.tnspro and rat.seqipd = isp.seqipd
      left outer join e085cli cli with (nolock) on ped.codcli = cli.codcli
      left outer join e075pro ser with (nolock) on isp.codpro = ser.codpro
    ";

    $where1 = "WHERE ped.codemp = 1 AND isp.codfam = :codFam1";
    $where2 = "WHERE ped.codemp = 1 AND isp.codfam = :codFam2";

    $sql = $querySer . "\n" . $where1 . "\n" . "UNION ALL\n" . $queryProd . "\n" . $where2;

    // echo "<pre>";
    // var_dump($CodFam);
    // var_dump($sql);
    // die();

    $stmt = $this->senior->prepare($sql);
    $stmt->execute([':codFam1' => $CodFam, ':codFam2' => $CodFam]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  }

  public function consultaCentroCusto(string $CodFam): array
  {
    $sql = "SELECT * FROM e012rat WHERE codemp = 1 AND codfam = :CodFam";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute(['CodFam' => $CodFam]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  }
}
