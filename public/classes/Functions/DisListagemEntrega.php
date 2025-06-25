<?php
require_once __DIR__ . '/../DBConnect.php';

class DisListagemEntrega
{
  // Conexões
  private $gestor;

  public function __construct()
  {
    $this->gestor = DatabaseConnection::getConnection('gestor');
  }

  public function ConsultaSetor()
  {
    $query = " SELECT DISTINCT codigoDoSetorDeEntrega AS Cod, nomeDoSetorDeEntrega AS Nome
		FROM disSetorDeEntrega 
		--WHERE codigoDoSetorDeEntrega IN (7,11,22,23,43,51,53,55,57,63,65,309,334) 
    ORDER BY Nome";

    $stmt = $this->gestor->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function ConsultaListagemEntregra($DtSelecionada, $setorSelecionado)
  {
    $query = "SELECT SetEnt.codigoDoSetorDeEntrega as Setor, SetEnt.nomeDoSetorDeEntrega as NomeSetor, ProSer.descricaoDoProdutoServico as Produto,Con.qtdJornaisContrato as Qtde, 
        Con.numeroDoContrato as Contrato, --vCadpessoaFisicaJuridica_2.nomeRazaoSocial as Assinante, 
        CONCAT (LTRIM(RTRIM(TipLog.siglaTipoLogradouro)),'. ', LTRIM(RTRIM(Logr.nomeDoLogradouro)), ' - ', LTRIM(RTRIM(LocEnt.numeroDoEndereco)), ' - ', LTRIM(RTRIM(Bai.nomeDoBairro)), ' - ', LTRIM(RTRIM(Mun.nomeDoMunicipio))) as Endereco, DtJornal = :d1,
        LocEnt.numeroDoEndereco as Numero, LocEnt.complementoDoEndereco as Complemento, DiaEnt.textoDiaDeEntrega
      FROM dbo.assContratos AS Con 
      INNER JOIN dbo.cadTipoDeAssinatura AS CadTipAss ON Con.codigoTipoAssinatura = CadTipAss.codigoTipoDeAssinatura 
      INNER JOIN dbo.cadPlanoDePagamentoDiasDeEntrega AS DiaEnt ON Con.codigoDoPlanoDePagamento = DiaEnt.codigoDoPlanoDePagamento 
      INNER JOIN dbo.cadProdutosServicos AS ProSer ON CadTipAss.codigoDoProdutoServico = ProSer.codigoDoProdutoServico 
      INNER JOIN dbo.assLocalDeEntregaContrato AS LocEntCon ON Con.numeroDoContrato = LocEntCon.numeroDoContrato 
      INNER JOIN dbo.vCadEnderecoDeEntrega AS EndEnt ON LocEntCon.codigoDoEnderecamento = EndEnt.codigoDoEnderecamento 
      INNER JOIN dbo.disSetorDeEntrega AS SetEnt ON EndEnt.codigoDoSetorDeEntrega = SetEnt.codigoDoSetorDeEntrega
      INNER JOIN dbo.assLocalDeEntrega AS LocEnt ON EndEnt.codigoDoEnderecamento = LocEnt.codigoDoEnderecamento
      INNER JOIN dbo.cadMeioDeEntrega AS MeiEnt ON LocEnt.codigoMeioDeEntrega = MeiEnt.codigoMeioDeEntrega
      INNER JOIN dbo.endLogradouros AS Logr ON LocEnt.codigoDoLogradouro = Logr.codigoDoLogradouro
      INNER JOIN dbo.endBairros AS Bai ON LocEnt.codigoDoBairro = Bai.codigoDoBairro
      INNER JOIN dbo.endMunicipios AS Mun ON Logr.codigoDoMunicipio = Mun.codigoDoMunicipio
      INNER JOIN dbo.endTipoLogradouro AS TipLog ON Logr.codigoDoTipoLogradouro = TipLog.codigoDoTipoLogradouro
      WHERE Con.situacaoDoContrato = 1 AND ProSer.codigoDoProdutoServico IN (1, 11) 
        AND Con.dataDevalidadeFinal >= :d2
        AND Con.dataDeValidadeInicial <= :d3
        AND LocEntCon.diaDaSemana = DATEPART(WEEKDAY, :d4)
        AND SUBSTRING(DiaEnt.textoDiaDeEntrega, DATEPART(WEEKDAY, :d5), 1) = 'S'
    ";

    if (isset($DtSelecionada) and $setorSelecionado !== '') {
      $query .=  " AND SetEnt.nomeDoSetorDeEntrega = :setorSelecionado ";
    }

    $query .= " ORDER BY SetEnt.nomeDoSetorDeEntrega";

    // echo "<pre>";
    // var_dump($query);
    // die();
    $stmt = $this->gestor->prepare($query);
    $stmt->bindValue(':d1', $DtSelecionada);
    $stmt->bindValue(':d2', $DtSelecionada);
    $stmt->bindValue(':d3', $DtSelecionada);
    $stmt->bindValue(':d4', $DtSelecionada);
    $stmt->bindValue(':d5', $DtSelecionada);
    if ($setorSelecionado !== '') {
      $stmt->bindParam(':setorSelecionado', $setorSelecionado);
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
