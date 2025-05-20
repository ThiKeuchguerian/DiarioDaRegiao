<?php
require_once __DIR__ . '/../DBConnect.php';

class ClassifCheckMetas
{
  // Conexões
  private $tecmidia;
  private $capt;

  // Declarando Query Padrao
  private $queryEasyClass = "SELECT cust.fullname AS Cliente, ad.ad_number AS CodAnuncio, i.pubdate AS DtPublicacao, ad.takedate AS DtCapitacao,
      SUBSTRING(CONVERT(VARCHAR(10), i.pubdate, 103), 4, 7) AS MesPub, i.charged_price AS VlrPub, sou.fullname COLLATE Latin1_General_CI_AI AS Origem, 
      sou.nickname COLLATE Latin1_General_CI_AI AS SL, bhad.fullname COLLATE Latin1_General_CI_AI AS Cobranca, ad.adtaker AS QuemCaptou, 
      ad.last_changed_by AS Alterou, u.fullname COLLATE Latin1_General_CI_AI AS CapComi,Total = 0, c.fullname AS NumContrato,
      con.external_code COLLATE Latin1_General_CI_AI AS CodVendCont,
      CASE 
        WHEN con.external_code IS NULL THEN u.fullname COLLATE Latin1_General_CI_AI 
        ELSE con.nickname COLLATE Latin1_General_CI_AI 
      END AS VendContrato
    FROM EC_AD ad WITH (noLock)
      INNER JOIN EC_Source sou WITH (noLock) ON ad.source_id = sou.source_id
      INNER JOIN EC_Customer cust WITH (noLock) ON ad.customer_id = cust.customer_id
      INNER JOIN EC_Insertion i WITH (noLock) ON ad.AD_ID = i.AD_ID
      LEFT OUTER JOIN EC_Billhow bhad WITH (noLock) on bhad.billhow_id = ad.billhow_id
      INNER JOIN EC_User u WITH (noLock) ON ad.adtakerbill = u.user_id
      LEFT OUTER JOIN EC_Contract c WITH (noLock) ON ad.contract_id = c.contract_id
      LEFT OUTER JOIN EC_ContractContact cc WITH (noLock) ON c.contract_id = cc.contract_id
      LEFT OUTER JOIN EC_Contact con WITH (noLock) ON cc.contact_id = con.contact_id
  ";

  private $queryCapt = "SELECT cli.nomeFantasia COLLATE Latin1_General_CI_AI AS Cliente, con.nroContrato AS CodAnuncio, dtcon.dataVeiculacao AS DtPublicacao, 
      con.dataEmissao AS DtCapitacao, FORMAT(dtcon.dataVeiculacao, 'MM/yyyy') AS MesPub, FORMAT(con.valor / t.TotalDatas, '0.00', 'pt-br') AS VlrPub, NULL AS CodVendCont,
      'Capt' COLLATE Latin1_General_CI_AI AS Origem, '' COLLATE Latin1_General_CI_AI AS SL, CONVERT(VARCHAR,con.formaPgto) AS Cobranca,
      con.idUsuCadastro AS QuemCaptou, con.idUsuAlteracao AS Alterou, '' COLLATE Latin1_General_CI_AI AS CapComi, '' COLLATE Latin1_General_CI_AI AS NumContrato,
      CASE 
        WHEN con.codVendedor = 578 THEN 'Silvania'
        WHEN con.codVendedor = 651 THEN 'Rosana'
        WHEN con.codVendedor = 1436 THEN 'Carolini'
      END AS VendContrato
    FROM contratos con
      INNER JOIN contratos_datas dtcon WITH (noLock) ON con.nroContrato = dtcon.nroContrato
      INNER JOIN vendedores ven WITH (noLock) ON con.codVendedor = ven.codVendedor
      INNER JOIN clientes cli WITH (noLock) ON con.cpfCnpj = cli.cpfCnpj
      CROSS APPLY ( SELECT COUNT(*) AS TotalDatas FROM contratos_datas x WHERE x.nroContrato = con.nroContrato ) t 
  ";

  private $queryCaptDia = "SELECT cli.nomeFantasia COLLATE Latin1_General_CI_AI AS Cliente, con.nroContrato AS CodAnuncio, dtcon.dataVeiculacao AS DtPublicacao, 
      con.dataEmissao AS DtCapitacao, FORMAT(dtcon.dataVeiculacao, 'MM/yyyy') AS MesPub, (con.valor / t.TotalDatas) AS VlrPub, NULL AS CodVendCont,
      'Capt' COLLATE Latin1_General_CI_AI AS Origem, '' COLLATE Latin1_General_CI_AI AS SL, CONVERT(VARCHAR,con.formaPgto) AS Cobranca,
      con.idUsuCadastro AS QuemCaptou, con.idUsuAlteracao AS Alterou, '' COLLATE Latin1_General_CI_AI AS CapComi, '' COLLATE Latin1_General_CI_AI AS NumContrato,
      CASE 
        WHEN con.codVendedor = 578 THEN 'Silvania'
        WHEN con.codVendedor = 651 THEN 'Rosana'
        WHEN con.codVendedor = 1436 THEN 'Carolini'
      END AS VendContrato
    FROM contratos con
      INNER JOIN contratos_datas dtcon WITH (noLock) ON con.nroContrato = dtcon.nroContrato
      INNER JOIN vendedores ven WITH (noLock) ON con.codVendedor = ven.codVendedor
      INNER JOIN clientes cli WITH (noLock) ON con.cpfCnpj = cli.cpfCnpj
      CROSS APPLY ( SELECT COUNT(*) AS TotalDatas FROM contratos_datas x WHERE x.nroContrato = con.nroContrato ) t 
  ";

  private $ajustandoNomeVendedor = "AjustandoNomeVendedor AS (	
    Select CodAnuncio, Cliente, DtCapitacao, DtPublicacao, MesPub, VlrPub, SL, Origem, Cobranca, QuemCaptou, CapComi, NumContrato, CodVendCont, 
      CASE 
        WHEN VendContrato IN ('rosana','rosana1_fn','rosana1_ba') THEN 'Rosana'
        WHEN VendContrato IN ('Carolini Freitas','carolini_fn','carolini_ba') THEN 'Carolini'
        WHEN VendContrato IN ('silvania','silvania_fn') THEN 'Silvania'
        ELSE VendContrato
      END AS VendContrato
    FROM PorVendedor
    GROUP BY CodAnuncio, Cliente, DtCapitacao, DtPublicacao, MesPub, VlrPub, SL, Origem, Cobranca, QuemCaptou, CapComi, NumContrato, CodVendCont, VendContrato
  )";

  private $filtrandoVendedor = "
    Select CodAnuncio, Cliente, DtCapitacao, DtPublicacao, MesPub, VlrPub, SL, Origem, Cobranca, QuemCaptou, CapComi, NumContrato, CodVendCont, VendContrato
    FROM AjustandoNomeVendedor
    WHERE VendContrato IN ('Carolini','Rosana','Silvania')
    GROUP BY CodAnuncio, Cliente, DtCapitacao, DtPublicacao, MesPub, VlrPub, SL, Origem, Cobranca, QuemCaptou, CapComi, NumContrato, CodVendCont, VendContrato
  ";

  public function __construct()
  {
    $this->tecmidia = DatabaseConnection::getConnection('tecmidia');
    $this->capt = DatabaseConnection::getConnection('capt');
  }

  public function ConsultaAno($Ano)
  {
    $whereEasyClassAno = " WHERE i.pubdate BETWEEN '{$Ano}0101' AND '{$Ano}1231'
      AND (ad.status_id != 1 AND  ad.queue_blocked = 0)
      AND (bhad.fullname NOT IN ('Bonificado','Cortesia','Compensação') OR bhad.fullname IS NULL)
      AND sou.fullname <> 'Paginação'
    ";

    $whereCaptAno = " WHERE dtcon.dataVeiculacao BETWEEN '{$Ano}0101' AND '{$Ano}1231' AND ven.codVendedor IN (578,651,1436) ";

    $querySomaAnoEasyClass = "WITH PorVendedor AS ( " . $this->queryEasyClass . $whereEasyClassAno . " ), " .
      $this->ajustandoNomeVendedor . $this->filtrandoVendedor . " ORDER BY VendContrato";
    // echo "<pre>";
    // var_dump($querySomaAnoEasyClass);
    // die();
    $SomaAnoEasyClass = $this->tecmidia->prepare($querySomaAnoEasyClass);
    $SomaAnoEasyClass->execute();
    $rowsEasyClass = $SomaAnoEasyClass->fetchAll(PDO::FETCH_ASSOC);

    $querySomaAnocapt = "WITH PorVendedor AS ( " . $this->queryCapt . $whereCaptAno . " ), " .
      $this->ajustandoNomeVendedor . $this->filtrandoVendedor . " ORDER BY VendContrato";
    // echo "<pre>";
    // var_dump($querySomaAnocapt);
    // die();
    $SomaAnoCapt = $this->capt->prepare($querySomaAnocapt);
    $SomaAnoCapt->execute();
    $rowsCapt = $SomaAnoCapt->fetchAll(PDO::FETCH_ASSOC);

    return array_merge($rowsEasyClass, $rowsCapt);
  }

  public function ConsultaDia($MesAno)
  {
    if ($MesAno === '') {
      $whereEasyClassDia = " WHERE ad.takedate BETWEEN DATEADD(MONTH, DATEDIFF(MONTH, 0, GETDATE()), 0) AND DATEADD(DAY, -1, DATEADD(MONTH, DATEDIFF(MONTH, 0, GETDATE()) + 1, 0))
        AND i.pubdate BETWEEN DATEADD(MONTH, DATEDIFF(MONTH, 0, GETDATE()), 0) AND DATEADD(DAY, -1, DATEADD(MONTH, DATEDIFF(MONTH, 0, GETDATE()) + 1, 0))
        AND (ad.status_id != 1 AND ad.queue_blocked = 0)
        AND (bhad.fullname NOT IN ('Bonificado', 'Cortesia', 'Compensação') OR bhad.fullname IS NULL)
        AND sou.fullname <> 'Paginação' 
      ";

      $whereCaptDia = " 
        WHERE dtcon.dataVeiculacao BETWEEN DATEADD(MONTH, DATEDIFF(MONTH, 0, GETDATE()), 0) 
          AND DATEADD(DAY, -1, DATEADD(MONTH, DATEDIFF(MONTH, 0, GETDATE()) + 1, 0))
          AND ven.codVendedor IN (578,651,1436)
      ";
    } elseif ($MesAno !== '') {
      $whereEasyClassDia = " WHERE SUBSTRING(CONVERT(VARCHAR(10), ad.takedate, 103), 4, 7) = :MesAno1
        AND SUBSTRING(CONVERT(VARCHAR(10), i.pubdate, 103), 4, 7) = :MesAno2
        AND (ad.status_id != 1 AND ad.queue_blocked = 0)
        AND (bhad.fullname NOT IN ('Bonificado', 'Cortesia', 'Compensação') OR bhad.fullname IS NULL)
        AND sou.fullname <> 'Paginação' 
      ";

      $whereCaptDia = " WHERE FORMAT(dtcon.dataVeiculacao, 'MM/yyyy') = :MesAno 
        AND ven.codVendedor IN (578,651,1436)
      ";
    }

    $querySomaDiaEasyClass = "WITH PorVendedor AS ( "
      . $this->queryEasyClass . $whereEasyClassDia . " ), " 
      . $this->ajustandoNomeVendedor . $this->filtrandoVendedor . " ORDER BY VendContrato";

    $SomaDiaEasyClass = $this->tecmidia->prepare($querySomaDiaEasyClass);
    if ($MesAno !== '') {
      $SomaDiaEasyClass->bindValue(":MesAno1", $MesAno);
      $SomaDiaEasyClass->bindValue(":MesAno2", $MesAno);
    }
    // echo "<pre>";
    // var_dump($querySomaDiaEasyClass);
    // die();
    $SomaDiaEasyClass->execute();
    $roysEasyClass = $SomaDiaEasyClass->fetchAll(PDO::FETCH_ASSOC);

    $querySomaDiaCapt = "WITH PorVendedor AS ( " 
      . $this->queryCapt . $whereCaptDia . " ), " 
      . $this->ajustandoNomeVendedor . $this->filtrandoVendedor 
      . " ORDER BY VendContrato, DtCapitacao, Cliente";

    $SomaDiaCapt = $this->capt->prepare($querySomaDiaCapt);
    // echo "<pre>";
    // var_dump($querySomaDiaCapt);
    // die();
    if ($MesAno !== '') {
      $SomaDiaCapt->bindValue(':MesAno', $MesAno);
    }
    $SomaDiaCapt->execute();
    $roysCapt = $SomaDiaCapt->fetchAll(PDO::FETCH_ASSOC);

    return array_merge($roysEasyClass, $roysCapt);
  }
}
