<?php
require_once __DIR__ . '/../DBConnect.php';

class CirVendasEquipe
{
  // Conexões
  private $DrGestor;

  public function __construct()
  {
    $this->DrGestor = DatabaseConnection::getConnection('DrGestor');
  }

  public function ConsultaVendasTelevendas($MesCad1, $MesCad2, $MesCad3, $MesCad4, $CodProduto)
  {
    $query = "SELECT NomeVendedor,
      CAST(SUM(CASE WHEN NatContrato = 'Combo' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad1' THEN 0.5 ELSE 0 END) AS INT) AS TotalCombo1,
      SUM(CASE WHEN NatContrato = 'Digital' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad1' THEN 1 ELSE 0 END) AS TotalDigital1,
      SUM(CASE WHEN NatContrato = 'Impressa' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad1' THEN 1 ELSE 0 END) AS TotalImpressa1,
      CAST(SUM(CASE
        WHEN NatContrato = 'Combo' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad1' THEN 0.5
        WHEN NatContrato = 'Digital' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad1' THEN 1
        WHEN NatContrato = 'Impressa' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad1' THEN 1
        ELSE 0
      END) AS INT) AS TotalGeral1,
      
      CAST(SUM(CASE WHEN NatContrato = 'Combo' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad2' THEN 0.5 ELSE 0 END) AS INT) AS TotalCombo2,
      SUM(CASE WHEN NatContrato = 'Digital' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad2' THEN 1 ELSE 0 END) AS TotalDigital2,
      SUM(CASE WHEN NatContrato = 'Impressa' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad2' THEN 1 ELSE 0 END) AS TotalImpressa2,
      CAST(SUM(CASE
        WHEN NatContrato = 'Combo' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad2' THEN 0.5
        WHEN NatContrato = 'Digital' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad2' THEN 1
        WHEN NatContrato = 'Impressa' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad2' THEN 1
        ELSE 0
      END) AS INT) AS TotalGeral2,

      CAST(SUM(CASE WHEN NatContrato = 'Combo' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad3' THEN 0.5 ELSE 0 END) AS INT) AS TotalCombo3,
      SUM(CASE WHEN NatContrato = 'Digital' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad3' THEN 1 ELSE 0 END) AS TotalDigital3,
      SUM(CASE WHEN NatContrato = 'Impressa' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad3' THEN 1 ELSE 0 END) AS TotalImpressa3,
      CAST(SUM(CASE
        WHEN NatContrato = 'Combo' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad3' THEN 0.5
        WHEN NatContrato = 'Digital' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad3' THEN 1
        WHEN NatContrato = 'Impressa' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad3' THEN 1
        ELSE 0
      END) AS INT) AS TotalGeral3,

      CAST(SUM(CASE WHEN NatContrato = 'Combo' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad4' THEN 0.5 ELSE 0 END) AS INT)  AS TotalCombo4,
      SUM(CASE WHEN NatContrato = 'Digital' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad4' THEN 1 ELSE 0 END) AS TotalDigital4,
      SUM(CASE WHEN NatContrato = 'Impressa' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad4' THEN 1 ELSE 0 END) AS TotalImpressa4,
      CAST(SUM(CASE
        WHEN NatContrato = 'Combo' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad4' THEN 0.5
        WHEN NatContrato = 'Digital' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad4' THEN 1
        WHEN NatContrato = 'Impressa' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad4' THEN 1
        ELSE 0
      END) AS INT) AS TotalGeral4,

      CAST(SUM(CASE
        WHEN NatContrato = 'Combo' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) IN ('$MesCad1', '$MesCad2', '$MesCad3', '$MesCad4') THEN 0.50
        WHEN NatContrato = 'Digital' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) IN ('$MesCad1', '$MesCad2', '$MesCad3', '$MesCad4') THEN 1
        WHEN NatContrato = 'Impressa' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) IN ('$MesCad1', '$MesCad2', '$MesCad3', '$MesCad4') THEN 1
        ELSE 0
      END) AS INT) AS TotalGeral

      FROM (
        SELECT DrCon.MesCad, Eq.NomeVendedor, DrCon.NatContrato
        FROM Dr_CadContratos DrCon
        LEFT OUTER JOIN gestor.dbo.vCadPessoaFisicaJuridica Pes ON Pes.codigoDaPessoa = DrCon.codigoDaPessoaVendedor
        LEFT OUTER JOIN Dr_EquipeVendas Eq ON Eq.CodVendedor = DrCon.codigoDaPessoaVendedor
        INNER JOIN gestor.dbo.assContratos Con WITH (NOLOCK) ON DrCon.NumerodoContrato = Con.numeroDoContrato
        INNER JOIN gestor.dbo.cadTipoDeAssinatura Tp WITH (NOLOCK) ON Tp.codigoTipoDeAssinatura = Con.codigoTipoAssinatura
        INNER JOIN gestor.dbo.cadProdutosServicos Sr WITH (NOLOCK) ON Sr.codigoDoProdutoServico = Tp.codigoDoProdutoServico
        WHERE DrCon.TipoCobranca = 'PAGO'
        AND DrCon.tipoDeContrato = 'I'
        AND (SUBSTRING(DrCon.MesCad,5,2) + '/' + SUBSTRING(DrCon.MesCad,1,4)) IN ('$MesCad1','$MesCad2','$MesCad3', '$MesCad4')
    ";

    if ($CodProduto === '1') {
      $query .= " AND Sr.codigoDoProdutoServico = 1 AND Eq.GrupoEquipe = '1-TELEVENDAS') AS Subquery GROUP BY NomeVendedor ORDER BY NomeVendedor;";
    } elseif ($CodProduto === '3') {
      $query .= " AND Sr.codigoDoProdutoServico = 3 AND Eq.GrupoEquipe = '1-TELEVENDAS') AS Subquery GROUP BY NomeVendedor ORDER BY NomeVendedor;";
    } elseif ($CodProduto === '11') {
      $query .= " AND Sr.codigoDoProdutoServico = 11 AND Eq.GrupoEquipe = '1-TELEVENDAS') AS Subquery GROUP BY NomeVendedor ORDER BY NomeVendedor;";
    } elseif ($CodProduto === '0') {
      $query .= " AND Sr.codigoDoProdutoServico IN (1,3,11) AND Eq.GrupoEquipe = '1-TELEVENDAS') AS Subquery GROUP BY NomeVendedor ORDER BY NomeVendedor;";
    } elseif ($CodProduto === '13') {
      $query .= " AND Sr.codigoDoProdutoServico IN (1,3) AND Eq.GrupoEquipe = '1-TELEVENDAS') AS Subquery GROUP BY NomeVendedor ORDER BY NomeVendedor;";
    }

    $stmt = $this->DrGestor->prepare($query);
    // echo "<pre>";
    // var_dump($stmt);
    // die();
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  }

  public function ConsultaVendasDepartamento($MesCad1, $MesCad2, $MesCad3, $MesCad4, $CodProduto)
  {
    $query = "SELECT GrupoEquipe,
      CAST(SUM(CASE WHEN NatContrato = 'Combo' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad1' THEN 0.5 ELSE 0 END) AS INT) AS TotalCombo1,
      SUM(CASE WHEN NatContrato = 'Digital' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad1' THEN 1 ELSE 0 END) AS TotalDigital1,
      SUM(CASE WHEN NatContrato = 'Impressa' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad1' THEN 1 ELSE 0 END) AS TotalImpressa1,
      CAST(SUM(CASE
        WHEN NatContrato = 'Combo' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad1' THEN 0.5
        WHEN NatContrato = 'Digital' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad1' THEN 1
        WHEN NatContrato = 'Impressa' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad1' THEN 1
        ELSE 0
      END) AS INT) AS TotalGeral1,
      
      CAST(SUM(CASE WHEN NatContrato = 'Combo' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad2' THEN 0.5 ELSE 0 END) AS INT) AS TotalCombo2,
      SUM(CASE WHEN NatContrato = 'Digital' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad2' THEN 1 ELSE 0 END) AS TotalDigital2,
      SUM(CASE WHEN NatContrato = 'Impressa' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad2' THEN 1 ELSE 0 END) AS TotalImpressa2,
      CAST(SUM(CASE
        WHEN NatContrato = 'Combo' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad2' THEN 0.5
        WHEN NatContrato = 'Digital' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad2' THEN 1
        WHEN NatContrato = 'Impressa' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad2' THEN 1
        ELSE 0
      END) AS INT) AS TotalGeral2,

      CAST(SUM(CASE WHEN NatContrato = 'Combo' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad3' THEN 0.5 ELSE 0 END) AS INT) AS TotalCombo3,
      SUM(CASE WHEN NatContrato = 'Digital' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad3' THEN 1 ELSE 0 END) AS TotalDigital3,
      SUM(CASE WHEN NatContrato = 'Impressa' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad3' THEN 1 ELSE 0 END) AS TotalImpressa3,
      CAST(SUM(CASE
        WHEN NatContrato = 'Combo' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad3' THEN 0.5
        WHEN NatContrato = 'Digital' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad3' THEN 1
        WHEN NatContrato = 'Impressa' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad3' THEN 1
        ELSE 0
      END) AS INT) AS TotalGeral3,

      CAST(SUM(CASE WHEN NatContrato = 'Combo' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad4' THEN 0.5 ELSE 0 END) AS INT)  AS TotalCombo4,
      SUM(CASE WHEN NatContrato = 'Digital' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad4' THEN 1 ELSE 0 END) AS TotalDigital4,
      SUM(CASE WHEN NatContrato = 'Impressa' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad4' THEN 1 ELSE 0 END) AS TotalImpressa4,
      CAST(SUM(CASE
        WHEN NatContrato = 'Combo' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad4' THEN 0.5
        WHEN NatContrato = 'Digital' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad4' THEN 1
        WHEN NatContrato = 'Impressa' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) = '$MesCad4' THEN 1
        ELSE 0
      END) AS INT) AS TotalGeral4, 

      CAST(SUM(CASE
        WHEN NatContrato = 'Combo' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) IN ('$MesCad1', '$MesCad2', '$MesCad3', '$MesCad4') THEN 0.50
        WHEN NatContrato = 'Digital' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) IN ('$MesCad1', '$MesCad2', '$MesCad3', '$MesCad4') THEN 1
        WHEN NatContrato = 'Impressa' AND (SUBSTRING(MesCad,5,2) + '/' + SUBSTRING(MesCad,1,4)) IN ('$MesCad1', '$MesCad2', '$MesCad3', '$MesCad4') THEN 1
        ELSE 0
      END) AS INT) AS TotalGeral

      FROM (
        SELECT DrCon.MesCad, Eq.GrupoEquipe, DrCon.NatContrato
        FROM Dr_CadContratos DrCon
        LEFT OUTER JOIN gestor.dbo.vCadPessoaFisicaJuridica Pes ON Pes.codigoDaPessoa = DrCon.codigoDaPessoaVendedor
        LEFT OUTER JOIN Dr_EquipeVendas Eq ON Eq.CodVendedor = DrCon.codigoDaPessoaVendedor
        INNER JOIN gestor.dbo.assContratos Con WITH (NOLOCK) ON DrCon.NumerodoContrato = Con.numeroDoContrato
        INNER JOIN gestor.dbo.cadTipoDeAssinatura Tp WITH (NOLOCK) ON Tp.codigoTipoDeAssinatura = Con.codigoTipoAssinatura
        INNER JOIN gestor.dbo.cadProdutosServicos Sr WITH (NOLOCK) ON Sr.codigoDoProdutoServico = Tp.codigoDoProdutoServico
        WHERE DrCon.TipoCobranca = 'PAGO'
        AND DrCon.tipoDeContrato = 'I'
        AND (SUBSTRING(DrCon.MesCad,5,2) + '/' + SUBSTRING(DrCon.MesCad,1,4)) IN ('$MesCad1','$MesCad2','$MesCad3', '$MesCad4')
    ";

    if ($CodProduto === '1') {
      $query .= " AND Sr.codigoDoProdutoServico = 1) AS Subquery GROUP BY GrupoEquipe ORDER BY GrupoEquipe;";
    } elseif ($CodProduto === '3') {
      $query .= " AND Sr.codigoDoProdutoServico = 3) AS Subquery GROUP BY GrupoEquipe ORDER BY GrupoEquipe;";
    } elseif ($CodProduto === '11') {
      $query .= " AND Sr.codigoDoProdutoServico = 11) AS Subquery GROUP BY GrupoEquipe ORDER BY GrupoEquipe;";
    } elseif ($CodProduto === '0') {
      $query .= " AND Sr.codigoDoProdutoServico IN (1,3,11)) AS Subquery GROUP BY GrupoEquipe ORDER BY GrupoEquipe;";
    } elseif ($CodProduto === '13') {
      $query .= " AND Sr.codigoDoProdutoServico IN (1,3)) AS Subquery GROUP BY GrupoEquipe ORDER BY GrupoEquipe;";
    }

    $stmt = $this->DrGestor->prepare($query);
    // echo "<pre>";
    // var_dump($stmt);
    // die();
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  }
}
