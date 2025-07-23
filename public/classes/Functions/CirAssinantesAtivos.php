<?php
require_once __DIR__ . '/../DBConnect.php';

class AssinantesAtivos
{
  // Conexões
  private $gestor;
  private $DrGestor;

  public function __construct()
  {
    $this->gestor = DatabaseConnection::getConnection('gestor');
    $this->DrGestor = DatabaseConnection::getConnection('DrGestor');
  }

  private function obterPrimeiroUltimoDia(string $mesCad): array
  {
    $data = \DateTime::createFromFormat('m/Y', $mesCad);
    if (! $data) {
      throw new \InvalidArgumentException("Mês/Ano inválido: {$mesCad}");
    }
    $primeiro = $data->format('Ym01');               // YYYY-mm-01
    $ultimo    = $data->modify('last day of this month')->format('Ymd');
    // echo "<pre>";
    // var_dump($primeiro, $ultimo);
    // die();
    return [$primeiro, $ultimo];
  }

  public function consultaProduto(): array
  {
    $query =
      "SELECT CProd.codigoDoProdutoServico AS CodProd, CProd.descricaoDoProdutoServico AS DesProd 
        FROM cadProdutosServicos CProd 
        WHERE CProd.codigoDoProdutoServico IN (1,3,11)
      ";

    // Prepara e executa
    $stmt = $this->gestor->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Retorna os vendedores ativos,
   * unindo Dr_EquipeVendas (no DB dw) com segUsuario (no DB gestor).
   *
   * @return array [['CodVend'=>'…','NomVend'=>'…'], …]
   */
  public function consultaVendedor(): array
  {
    $sql =
      "SELECT
          Ven.CodVendedor AS CodVend,
          Ven.NomeVendedor AS NomVend
        FROM Dr_EquipeVendas AS Ven
        INNER JOIN gestor.dbo.segUsuario AS GUse
          ON GUse.codigoDaPessoa = CAST(Ven.CodVendedor AS CHAR)
        AND GUse.idAtivo = 1
        ORDER BY Ven.NomeVendedor
      ";

    $stmt = $this->DrGestor->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  public function consultaContratos($dados): array
  {
    $dtInicio   = $dados['dtInicio'];
    $dtFim      = $dados['dtFim'];
    $codProduto = $dados['codProduto'];
    $combo      = $dados['Combo'];
    $codVend    = $dados['CodVend'];
    $mesCad     = $dados['MesAno'];
    $codVend    = $dados['CodVend'];
    $tipoCon    = $dados['TipoCon']; // I ou R
    $tipoCob    = $dados['TipoCob']; // Pago ou Cortesia

    $sql =
      " SELECT
          CASE 
            WHEN PL.descricaoDoPlanoDePagamento LIKE '%CBO%' THEN '13'
            ELSE Tp.codigoDoProdutoServico
          END AS Produto,
          CASE 
            WHEN TpCob.codigoTipoCobranca = '7' THEN 'Cortesia'
            ELSE 'Paga'
          END AS TipoCob,
          Pl.descricaoDoPlanoDePagamento, St.descricaoSituacao, con.numeroDoContrato,
          Con.dataDaAssinatura, Con.dataDeValidadeInicial, Con.dataDeValidadeFinal,
          Pes.nomeRazaoSocial, Pes.identMF as CPF_CNPJ, Pes.codigoDaPessoa as Assinante, 
          PesV.codigoDaPessoa as CodVendedor, PesV.nomeRazaoSocial as Vendedor,
          Con.qtdJornaisContrato, Con.valorTotal, Tp.descricaoTipoDeAssinatura,
          Con.tipoDeContrato, Sr.descricaoDoProdutoServico, TpCob.descricaoTipoCobranca,
          MtC.descrDoMotivoDeCancelamento, Con.codigoDoMotivoDeCancelamento,
          Con.dataDeCancelamento, Con.quantidadeDeParcelasDoContrato,
          Con.numeroDeExemplares, Con.numeroDeExemplaresEntregues, Con.numeroDeExemplaresPagos,
          dbo.dr_ContratoPai(Con.numerodocontrato) as ContratoPai,
          dbo.dr_BaixouPrimParcela(Con.numerodocontrato) as BaixouPrimParc,
          Con.valorTotal, Con.numeroDeExemplares, Con.numeroDeExemplaresEntregues, Con.numeroDeExemplaresPagos,
          dbo.dr_RetTipoContrato(con.numerodocontrato) as NatContrato,
          substring(dbo.fc_drPiece(dbo.dr_ContratoPai(Con.numerodocontrato),'^',2),7,2)+'/'+
          substring(dbo.fc_drPiece(dbo.dr_ContratoPai(Con.numerodocontrato),'^',2),5,2)+'/'+
          substring(dbo.fc_drPiece(dbo.dr_ContratoPai(Con.numerodocontrato),'^',2),1,4) as DtContratoPai
        FROM assContratos Con WITH (NOLOCK)
          INNER JOIN cadPlanoDePagamento Pl WITH (NOLOCK) ON Pl.codigoDoPlanoDePagamento = Con.codigoDoPlanoDePagamento
          INNER JOIN cadSituacao St WITH (NOLOCK) ON St.codigoSituacao = Con.situacaoDoContrato
          INNER JOIN vCadpessoaFisicaJuridica Pes WITH (NOLOCK) ON Pes.codigodapessoa = Con.codigodapessoa
          INNER JOIN vCadpessoaFisicaJuridica PesV WITH (NOLOCK) ON PesV.codigodapessoa = Con.codigoDaPessoaVendedor
          INNER JOIN cadTipoDeAssinatura Tp WITH (NOLOCK) ON Tp.codigoTipoDeAssinatura = Con.codigoTipoAssinatura
          INNER JOIN cadProdutosServicos Sr WITH (NOLOCK) ON Sr.codigoDoProdutoServico = Tp.codigoDoProdutoServico
          INNER JOIN assDadosParaCobranca Cob WITH (NOLOCK) ON Cob.identificadorCobranca = Con.identificadorCobranca
          INNER JOIN cadTiposDeDadoParaCobranca TpCob WITH (NOLOCK) ON TpCob.codigoTipoCobranca = Cob.codigoTipoCobranca
          LEFT OUTER JOIN cadMotivoDeCancelamento MtC WITH (NOLOCK) ON MtC.codigoDoMotivoDeCancelamento = Con.codigoDoMotivoDeCancelamento
          LEFT OUTER JOIN assRenovacoes Ren WITH (NOLOCK) ON Ren.numeroDoContratoAnterior = Con.numeroDoContrato
      ";

    $where = [];
    $params = [];

    if (!empty($dtInicio) && !empty($dtFim)) {
      $where[] = "(Con.dataDaAssinatura BETWEEN :dtInicio AND :dtFim)";
      $params[':dtInicio'] = $dtInicio;
      $params[':dtFim'] = $dtFim;
    }
    if (!empty($codProduto)) {
      $where[] = "Sr.codigoDoProdutoServico = :codProduto";
      $params[':codProduto'] = $codProduto;
    }
    if (!empty($codVend)) {
      $where[] = "Eq.CodVendedor = :codVend";
      $params[':codVend'] = $codVend;
    }
    if (!empty($mesCad)) {
      list($incioMesCad, $fimMesCad) = $this->obterPrimeiroUltimoDia($mesCad);
      $where[] = "(Con.dataDaAssinatura BETWEEN :incioMesCad AND :fimMesCad)";
      $params[':incioMesCad'] = $incioMesCad;
      $params[':fimMesCad'] = $fimMesCad;
    }
    if (!empty($tipoCon)) {
      $where[] = "Con.tipoDeContrato = :tipoCon";
      $params[':tipoCon'] = $tipoCon;
    }
    if (!empty($combo) && $combo === 'S') {
      $where[] = "NatContrato = 'Combo'";
    } else if (!empty($combo) && $combo === 'N') {
      $where[] = "NatContrato <> 'Combo'";
    }
    if (!empty($tipoCob) && $tipoCob === 'Pago') {
      $where[] = "TpCob.codigoTipoCobranca <> '7'";
    } else if (!empty($tipoCob) && $tipoCob === 'Cortesia') {
      $where[] = "TpCob.codigoTipoCobranca = '7'";
    }

    if (count($where) > 0) {
      $sql .= "\n WHERE " . implode(" AND ", $where);
    } else {
      $sql .= "\n WHERE Con.situacaoDoContrato IN (1,3) ";
      $sql .= "\n AND Con.dataDeValidadeInicial >= GETDATE() AND Con.dataDeValidadeFinal <= GETDATE()";
    }

    $sql .= "\n ORDER BY Con.dataDaAssinatura, Pes.nomeRazaoSocial, Con.numeroDoContrato";

    $stmt = $this->gestor->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
