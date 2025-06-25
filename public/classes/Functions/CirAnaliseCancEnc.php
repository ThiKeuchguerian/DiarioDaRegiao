<?php
require_once __DIR__ . '/../DBConnect.php';

class CirAnaliseCancEnc
{
  // Conexões
  private $gestor;

  public function __construct()
  {
    $this->gestor = DatabaseConnection::getConnection('gestor');
  }

  /**
   * @param array $meses     Ex: ['05/2025','06/2025','07/2025','08/2025']
   * @param int   $codProduto 0 (todos), 1, 3, 11 ou 13
   * @return array
   * @throws InvalidArgumentException
   */

  // Variáveis
  private  string $queryPadrao = "SELECT TRIM(MtC.descrDoMotivoDeCancelamento) AS MotivoCancelamento,
	  FORMAT(Con.dataDeCancelamento, 'MM/yyyy') AS MesCanc, COUNT(*) AS Quantidade
      FROM assContratos Con WITH (NOLOCK)
      LEFT OUTER JOIN cadMotivoDeCancelamento MtC WITH (NOLOCK) ON MtC.codigoDoMotivoDeCancelamento = Con.codigoDoMotivoDeCancelamento
      INNER JOIN cadTipoDeAssinatura Tp WITH (NOLOCK) ON Tp.codigoTipoDeAssinatura = Con.codigoTipoAssinatura 
      INNER JOIN cadProdutosServicos Sr WITH (NOLOCK) ON Sr.codigoDoProdutoServico = Tp.codigoDoProdutoServico
	    INNER JOIN assDadosParaCobranca Cob WITH (NOLOCK) ON Cob.identificadorCobranca = Con.identificadorCobranca 
      INNER JOIN cadTiposDeDadoParaCobranca TpCob WITH (NOLOCK) ON TpCob.codigoTipoCobranca = Cob.codigoTipoCobranca
      LEFT OUTER JOIN assRenovacoes Ren WITH (NOLOCK) ON Ren.numeroDoContratoAnterior  = Con.numeroDoContrato
      WHERE ( Con.dataDeValidadeInicial <= Con.dataDeCancelamento AND Con.dataDevalidadeFinal >= Con.dataDeCancelamento )
        AND MtC.codigoDoMotivoDeCancelamento IS NOT NULL -- Apenas registros com motivo de cancelamento
        AND MtC.codigoDoMotivoDeCancelamento NOT IN (115, 116, 117) 
        AND TpCob.descricaoTipoCobranca <> 'CORTESIA' -- Menos Cortesias
  ";

  private string $queryAnalitico = "SELECT Con.numeroDoContrato, Con.dataDaAssinatura, Cad.nomeRazaoSocial, TpCob.descricaoTipoCobranca, 
        TRIM(MtC.descrDoMotivoDeCancelamento) AS MotivoCancelamento, Tp.descricaoTipoDeAssinatura, 
        Sr.FORMAT(Con.dataDeCancelamento, 'MM/yyyy') AS MesCanc
      FROM assContratos Con WITH (NOLOCK)
      INNER JOIN vCadPessoaFisicaJuridica Cad WITH (NOLOCK) ON Cad.codigoDaPessoa = Con.codigoDaPessoa
      LEFT OUTER JOIN cadMotivoDeCancelamento MtC WITH (NOLOCK) ON MtC.codigoDoMotivoDeCancelamento = Con.codigoDoMotivoDeCancelamento
      INNER JOIN cadTipoDeAssinatura Tp WITH (NOLOCK) ON Tp.codigoTipoDeAssinatura = Con.codigoTipoAssinatura 
      INNER JOIN cadProdutosServicos Sr WITH (NOLOCK) ON Sr.codigoDoProdutoServico = Tp.codigoDoProdutoServico
      INNER JOIN assDadosParaCobranca Cob WITH (NOLOCK) ON Cob.identificadorCobranca = Con.identificadorCobranca 
      INNER JOIN cadTiposDeDadoParaCobranca TpCob WITH (NOLOCK) ON TpCob.codigoTipoCobranca = Cob.codigoTipoCobranca
      LEFT OUTER JOIN assRenovacoes Ren WITH (NOLOCK) ON Ren.numeroDoContratoAnterior  = Con.numeroDoContrato
      WHERE ( Con.dataDeValidadeInicial <= Con.dataDeCancelamento AND Con.dataDevalidadeFinal >= Con.dataDeCancelamento )
        AND MtC.codigoDoMotivoDeCancelamento IS NOT NULL -- Apenas registros com motivo de cancelamento
        AND MtC.codigoDoMotivoDeCancelamento NOT IN (115, 116, 117) 
        AND TpCob.descricaoTipoCobranca <> 'CORTESIA' -- Menos Cortesias
  ";

  public function ConsultaCancEnc(array $Meses, int $codProduto): array
  {
    // Validações mínimas
    if (count($Meses) < 1 || count($Meses) > 5) {
      throw new InvalidArgumentException("De 1 a 4 meses são permitidos");
    }

    // Monta filtro por produto (switch em vez de match)
    switch ($codProduto) {
      case 1:
        $filtroProduto = " AND Sr.codigoDoProdutoServico = 1";
        break;
      case 3:
        $filtroProduto = " AND Sr.codigoDoProdutoServico = 3";
        break;
      case 11:
        $filtroProduto = " AND Sr.codigoDoProdutoServico = 11";
        break;
      case 13:
        $filtroProduto = " AND Sr.codigoDoProdutoServico IN (1,3)";
        break;
      default:
        $filtroProduto = " AND Sr.codigoDoProdutoServico IN (1,3,11)";
        break;
    }

    // Monta Lista com os Meses
    $ListMeses = "'" . implode("', '", $Meses) . "'";

    // Monta Group BY e Order BY
    $GroupBy = " GROUP BY MtC.descrDoMotivoDeCancelamento, FORMAT(Con.dataDeCancelamento, 'MM/yyyy') ";
    $OrderBy = " ORDER BY MotivoCancelamento ";

    // 4) Monta os dois SELECTs e o UNION ALL
    $sql1 = $this->queryPadrao
      . $filtroProduto
      . " AND Con.situacaoDoContrato = '2' " // Somente Cancelados
      . " AND FORMAT(Con.dataDeCancelamento, 'MM/yyyy') IN ($ListMeses) "
      . $GroupBy;

    $sql2 = $this->queryPadrao
      . $filtroProduto
      . " AND Con.situacaoDoContrato = '4' " // Somente Encerrados
      . " AND Ren.numeroDoContratoAnterior IS NULL " // Somente não renovados
      . " AND FORMAT(Con.dataDeCancelamento, 'MM/yyyy') IN ($ListMeses) "
      . $GroupBy;

    $query = $sql1 . " UNION ALL "
      . $sql2 . $OrderBy;

    // Prepara, faz bind e executa
    $stmt = $this->gestor->prepare($query);
    // echo "<pre>";
    // var_dump($query);
    // var_dump($Meses);
    // die();
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function ConsultaAnalitica($MesAno, $codProduto)
  {
    // Monta filtro por produto (switch em vez de match)
    switch ($codProduto) {
      case 1:
        $filtroProduto = " AND Sr.codigoDoProdutoServico = 1";
        break;
      case 3:
        $filtroProduto = " AND Sr.codigoDoProdutoServico = 3";
        break;
      case 11:
        $filtroProduto = " AND Sr.codigoDoProdutoServico = 11";
        break;
      case 13:
        $filtroProduto = " AND Sr.codigoDoProdutoServico IN (1,3)";
        break;
      default:
        $filtroProduto = " AND Sr.codigoDoProdutoServico IN (1,3,11)";
        break;
    }

    $OrderBy = " ORDER BY MotivoCancelamento";
    //Monta os dois SELECTs e o UNION ALL
    $sql1 = $this->queryAnalitico
      . $filtroProduto
      . " AND Con.situacaoDoContrato = 2 " // Somente Cancelados
      . " AND FORMAT(Con.dataDeCancelamento, 'MM/yyyy') = :MesAno1 ";

    $sql2 = $this->queryAnalitico
      . $filtroProduto
      . " AND Con.situacaoDoContrato = '4' " // Somente Encerrados
      . " AND Ren.numeroDoContratoAnterior IS NULL " //Somente não renovados
      . " AND FORMAT(Con.dataDeCancelamento, 'MM/yyyy') = :MesAno2 ";

    $query = $sql1 . " UNION ALL " . $sql2 . $OrderBy;

    // Prepara, faz bind e executa
    $stmt = $this->gestor->prepare($query);
    if (!empty($MesAno)) {
      $stmt->bindValue(':MesAno1', $MesAno);
      $stmt->bindValue(':MesAno2', $MesAno);
    }
    // echo "<pre>";
    // var_dump($query);
    // var_dump($Meses);
    // die();
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
