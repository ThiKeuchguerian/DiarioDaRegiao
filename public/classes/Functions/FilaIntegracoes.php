<?php
require_once __DIR__ . '/../DBConnect.php';

class FilaIntegracoes
{
  private $senior;
  private $gestor;
  private $totvs;
  private $capt;

  public function __construct()
  {
    $this->senior = DatabaseConnection::getConnection('senior');
    $this->gestor = DatabaseConnection::getConnection('gestor');
    $this->totvs  = DatabaseConnection::getConnection('totvs');
    $this->capt   = DatabaseConnection::getConnection('capt');
  }

  //Integração de Cliente
  public function IntegracaoCliente()
  {
    $queryCliente = "SELECT Cli.usu_zr_cod AS Codigo, Cli.usu_zr_desc as Cliente, Cli.usu_zr_cgc AS CpfCnpj, Cli.usu_zr_inscr AS IE, Cli.usu_zr_inscrm AS IM, Cli.usu_zr_flproc AS FlaProc, Cli.usu_zr_dtgera AS DtGeracao, Cli.usu_zr_orisist AS Sistema,
    concat (LTRIM(RTRIM(Cli.usu_zr_end)),' ', ' - ', LTRIM(RTRIM(Cli.usu_zr_bairro)), ' - ', LTRIM(RTRIM(Cli.usu_zr_cep))) as Endereco, Cli.usu_zr_mun AS Municipio, Cli.usu_zr_errproc AS ErroIntegra
    FROM USU_TSZR010 AS Cli WHERE Cli.usu_zr_tipreg='C' AND Cli.usu_zr_flproc IN ('N','E');";
    $buscaCliente = $this->senior->prepare($queryCliente);
    $buscaCliente->execute();
    $dadosCliente = $buscaCliente->fetchAll(PDO::FETCH_ASSOC);
    return $dadosCliente;
  }
  //Integração Grafica/EasyClass
  public function IntegracaoGraficaEasyClass()
  {
    $queryGrafEasy = "SELECT Ped.usu_zp_numori AS NumPed, Ped.usu_zp_flproc AS FlProc, Ped.usu_zp_cliente AS CodCli, Ped.usu_zp_dtgera AS DtGera, Ped.usu_zp_lote AS Lote, 
	   Ped.usu_zp_origem AS Origem, Ped.usu_zp_tipocli AS TipoCli, 
	   Ped.usu_zp_valped AS VlrPedido,Ped.usu_zp_parc1 AS VlrParc,  Ped.usu_numped AS PedidoS, Ped.usu_obsproc AS Men 
  FROM usu_tszp010 Ped
  where Ped.usu_zp_flproc IN ('N','E');";
    $BuscaGrafEasy = $this->senior->prepare($queryGrafEasy);
    $BuscaGrafEasy->execute();
    $DadosGrafEasy = $BuscaGrafEasy->fetchAll(PDO::FETCH_ASSOC);
    return $DadosGrafEasy;
  }
  //Integração CaptWeb
  public function IntegracaoCaptWeb()
  {
    $queryPedCapt = "SELECT Ped.usu_lote AS Lote, Ped.usu_numori AS NCon, Ped.usu_datemi AS DtEm, Ped.usu_cliente AS Cli, Ped.usu_cpfcnpj AS CpfCnpj, Ped.usu_titanu AS Titulo,
    Ped.usu_contato AS Vend, Ped.usu_agencia AS Ag, Ped.usu_valor_doc AS Vlr, Ped.usu_status AS Status, Ped.usu_obsreg AS MenErro FROM usu_tpedcapt Ped
    WHERE Ped.usu_status IN ('N','E') --Ped.usu_numori=335298
    ORDER BY Ped.usu_datemi";
    $buscaPedCapt = $this->senior->prepare($queryPedCapt);
    $buscaPedCapt->execute();
    $dadosPedCapt = $buscaPedCapt->fetchAll(PDO::FETCH_ASSOC);
    return $dadosPedCapt;
  }
  //Integração Assinaturas Gestor
  public function IntegracaoAssinaturasGestor()
  {
    $queryAssGestor = "SELECT Ass.NumeroDoContrato AS NumContrato, Ass.CodigoPessoa_Gestor AS CodGestor, Ass.CodigoPessoa_ERP AS CodERP, Ass.CodigoDoProduto AS CodP, Ass.Produto AS Produto, Ass.Operacao AS Op, Ass.DtHr_Gravacao AS DtGra, Ass.TipoAssinatura AS TpAss, Ass.PlanoPagamento AS PlPgto, Ass.Situacao AS Sit 
    FROM USU_VFatAss Ass
    where DataDeCadastro >= convert(varchar(10), GETDATE(),103)";
    $buscaAssGestor = $this->senior->prepare($queryAssGestor);
    $buscaAssGestor->execute();
    $dadosAssGestor = $buscaAssGestor->fetchAll(PDO::FETCH_ASSOC);
    return $dadosAssGestor;
  }
  //Integração Bancas Gestor
  public function IntegracaoBancasGestor()
  {
    $queryBanGestor = "SELECT Ban.NumeroDoContrato AS NumCon,  Ban.Id AS ID, Ban.Produto AS Produto, Ban.Operacao, Ban.Situacao AS Sit 
    FROM USU_VFatBan Ban
    where DataDeCadastro >= convert(varchar(10), GETDATE(),103)";
    $buscaBanGestor = $this->senior->prepare($queryBanGestor);
    $buscaBanGestor->execute();
    $dadosBanGestor = $buscaBanGestor->fetchAll(PDO::FETCH_ASSOC);
    return $dadosBanGestor;
  }
  //Integração Cliente e Produto Protheus
  public function IntegracaoClienteProdutoProtheus()
  {
    $queryProdCliProtheus = "SELECT Cli.ZR_COD AS CodCli, Cli.ZR_DESC AS NomCli, Cli.ZR_CGC AS CpfCnpj, Cli.ZR_INSCR AS IE, 
    Cli.ZR_FLPROC AS FlagPro, Cli.ZR_DTGERA AS DtGer, Cli.ZR_ERRPROC AS MensagemErro,
    CASE 
      WHEN Cli.ZR_TIPREG = 'P' THEN 'Produto' 
      WHEN Cli.ZR_TIPREG = 'C' THEN 'Cliente' 
    END AS TipoReg
    FROM SZR010 Cli
    WHERE Cli.ZR_FLPROC IN ('N','E')";
    $buscaProdCliProtheus = $this->totvs->prepare($queryProdCliProtheus);
    $buscaProdCliProtheus->execute();
    $dadosProdCliProtheus = $buscaProdCliProtheus->fetchAll((PDO::FETCH_ASSOC));
    return $dadosProdCliProtheus;
  }
  //Integração Pedidos Protheus
  public function IntegracaoPedidosProtheus()
  {
    $queryPedProtheus = "SELECT Ped.ZP_FLPROC AS FPro, Ped.ZP_NUMORI AS Num, Ped.ZP_CLIENTE AS Cli, 
      Ped.ZP_EMISSAO AS DtEmi, Ped.ZP_DTGERA AS DtGe, Ped.ZP_VEND1 AS CodVen, Ped.D_E_L_E_T_ AS Del, 
      Iped.ZQ_PRODUTO AS CodProd, 
      PedInt.C5_NUM AS NumPed, PedInt.C5_LOTE AS LOTE, PedInt.C5_XNUMORI AS PedInt, Ped.ZP_ERRPROC AS Erro
    FROM  SZP010 Ped
    INNER JOIN SZQ010 Iped ON Ped.ZP_FILIAL = Iped.ZQ_FILIAL AND Ped.ZP_CLIENTE = Iped.ZQ_CLI AND Ped.ZP_NUMORI = Iped.ZQ_NUMORI
    LEFT JOIN SC5010 PedInt ON Ped.ZP_FILIAL = PedInt.C5_FILIAL AND Ped.ZP_NUMORI = PedInt.C5_XNUMORI AND Ped.ZP_LOTE = PedInt.C5_LOTE AND Ped.ZP_CLIENTE = PedInt.C5_CLIENTE
    WHERE Ped.ZP_FLPROC IN ('N','E') AND Ped.ZP_EMISSAO >= '20250101' ORDER BY Ped.ZP_DTGERA";
    $buscaPedprotheus = $this->totvs->prepare($queryPedProtheus);
    $buscaPedprotheus->execute();
    $dadosPedProtheus = $buscaPedprotheus->fetchAll((PDO::FETCH_ASSOC));
    return $dadosPedProtheus;
  }
}
