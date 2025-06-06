<?php
require_once __DIR__ . '/../DBConnect.php';

class CirComissao
{
  private $gestor;

  public function __construct()
  {
    $this->gestor = DatabaseConnection::getConnection('gestor');
  }

  /**
   * Queries recurring card payments between the given start and end dates.
   *
   * @param string $dtInicio Start date in 'YYYY-MM-DD' format
   * @param string $dtFim End date in 'YYYY-MM-DD' format
   * @return array Result set as an associative array
   */
  public function consultaComissao(string $dtInicio, string $dtFim): array
  {
    // adiciona a hora para formar o intervalo completo
    $inicio = $dtInicio . ' 00:00:00';
    $fim    = $dtFim    . ' 23:59:59';

    $sql =
      " WITH Comissao AS (
          SELECT assCon.codigoDaPessoa, assCon.situacaoDoContrato, assCon.dataDaAssinatura AS Data, assCon.numeroDoContrato, assCon.valorTotal, assCon.tipoDeContrato, assCon.codigoTipoAssinatura, 
            Cob.codigoTipoCobranca, assCon.codigoDaPessoaVendedor AS CodVen, 'V' AS Tipo, CondPg.percComisVendedor AS Peso, codigoDoProdutoServico
          FROM assContratos AS assCon WITH (nolock) 
          INNER JOIN assDadosParaCobranca AS Cob WITH (nolock) ON assCon.identificadorCobranca = Cob.identificadorCobranca 
          INNER JOIN cadCondicoesDePagamentoCobranca AS CondPg WITH (nolock) ON assCon.codigoDoPlanoDePagamento = CondPg.codigoDoPlanoDePagamento AND  assCon.codigoDaCondicaoDePagamento = CondPg.codigoDaCondicaoDePagamento AND Cob.codigoTipoCobranca = CondPg.codigoTipoCobranca 
          INNER JOIN cadTipoDeAssinatura AS TipAss WITH (nolock) ON assCon.codigoTipoAssinatura = TipAss.codigoTipoDeAssinatura AND CondPg.codigoTipoDeAssinatura = TipAss.codigoTipoDeAssinatura
          WHERE assCon.tipoDeContrato = 'I' AND assCon.codigoDaPessoaVendedor <> 1 
            AND assCon.situacaoDoContrato IN (1, 3) AND NOT (Cob.codigoTipoCobranca IN (7, 8))
          UNION ALL
          SELECT assCon.codigoDaPessoa, assCon.situacaoDoContrato, assCon.dataDeCancelamento AS Data, assCon.numeroDoContrato, assCon.valorTotal, assCon.tipoDeContrato, assCon.codigoTipoAssinatura, 
            Cob.codigoTipoCobranca, assCon.codigoDaPessoaVendedor AS CodVen, 'C' AS Tipo, CondPg.percComisVendedor AS Peso, codigoDoProdutoServico
          FROM assContratos AS assCon WITH (nolock) 
          INNER JOIN assDadosParaCobranca AS Cob WITH (nolock) ON assCon.identificadorCobranca = Cob.identificadorCobranca 
          INNER JOIN cadCondicoesDePagamentoCobranca CondPg WITH (nolock) ON assCon.codigoDoPlanoDePagamento = CondPg.codigoDoPlanoDePagamento AND  assCon.codigoDaCondicaoDePagamento = CondPg.codigoDaCondicaoDePagamento AND Cob.codigoTipoCobranca = CondPg.codigoTipoCobranca 
          INNER JOIN cadTipoDeAssinatura AS TipAss WITH (nolock) ON assCon.codigoTipoAssinatura = TipAss.codigoTipoDeAssinatura AND  CondPg.codigoTipoDeAssinatura = TipAss.codigoTipoDeAssinatura
          WHERE assCon.tipoDeContrato = 'I' AND assCon.situacaoDoContrato = 2 AND assCon.codigoDaPessoaVendedor <> 1 
            AND NOT (Cob.codigoTipoCobranca IN (7, 8))
          UNION ALL
          SELECT assCon.codigoDaPessoa, assCon.situacaoDoContrato, BxPg.dataDeCadastramento AS Data, assCon.numeroDoContrato, SUM(BxPg.valor) AS valorTotal, assCon.tipoDeContrato, 
            assCon.codigoTipoAssinatura, Cob.codigoTipoCobranca, assCon.codigoDaPessoaVendedor AS CodVen, 'R' AS Tipo, 0 AS Peso, codigoDoProdutoServico
          FROM assContratos AS assCon WITH (nolock) 
          INNER JOIN assFinanceiroDoContrato AS FinCon ON assCon.numeroDoContrato = FinCon.numeroDoContrato 
          INNER JOIN assBaixaPagamentos AS BxPg WITH (nolock) ON FinCon.numeroDoContrato = BxPg.numeroDoContrato AND FinCon.numeroDaParcela = BxPg.numeroDaParcela 
          INNER JOIN assDadosParaCobranca AS Cob WITH (nolock) ON assCon.identificadorCobranca = Cob.identificadorCobranca 
          INNER JOIN cadTipoDeAssinatura AS TipAss WITH (nolock) ON assCon.codigoTipoAssinatura = TipAss.codigoTipoDeAssinatura
          WHERE assCon.tipoDeContrato = 'I' AND assCon.codigoDaPessoaVendedor <> 1 AND BxPg.codigoDoMotivoDeBaixa = 1 
            AND NOT (Cob.codigoTipoCobranca IN (7, 8)) 
            AND BxPg.idStatus = 1
          GROUP BY assCon.codigoDaPessoa, assCon.situacaoDoContrato, BxPg.dataDeCadastramento, assCon.numeroDoContrato, 
            assCon.tipoDeContrato, assCon.codigoTipoAssinatura, Cob.codigoTipoCobranca, 
            assCon.codigoDaPessoaVendedor, codigoDoProdutoServico
        )
          SELECT Com.codigoDaPessoa as CodCli, Cad.nomeRazaoSocial AS NomeCli, 
            Com.numeroDoContrato AS NumCon, Com.situacaoDoContrato AS Status, Com.CodVen, Ven.nomeRazaoSocial AS NomeVen,
            PerCom = '12.5', Com.Data, Com.valorTotal, Com.Tipo, Com.Peso, Com.codigoDoProdutoServico AS CodPro
          FROM Comissao AS Com
          INNER JOIN vCadPessoaFisicaJuridica Cad WITH (NOLOCK) ON Com.codigoDaPessoa = Cad.codigoDaPessoa
          INNER JOIN vCadPessoaFisicaJuridica Ven WITH (NOLOCK) ON Com.CodVen = Ven.codigoDaPessoa
          WHERE Com.Data BETWEEN :dtInicio AND :dtFim
          ORDER BY NomeCli
      ";

    $stmt = $this->gestor->prepare($sql);
    $stmt->execute([
      ':dtInicio' => $dtInicio,
      ':dtFim' => $dtFim
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
