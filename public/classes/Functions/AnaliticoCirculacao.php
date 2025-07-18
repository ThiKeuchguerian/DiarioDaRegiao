<?php
require_once __DIR__ . '/../DBConnect.php';

class AnaliticoCirculacao
{
  // Conexões
  private $gestor;
  private $contaDiario;

  public function __construct()
  {
    $this->gestor = DatabaseConnection::getConnection('gestor');
    $this->contaDiario = DatabaseConnection::getConnection('contaDiario');
  }

  public function consultaContratos(): array
  {
    $sql =
      "SELECT C.numeroDoContrato,
        CASE 
          WHEN PP.descricaoDoPlanoDePagamento LIKE '%CBO%' THEN 'Combo'
          WHEN PP.descricaoDoPlanoDePagamento NOT LIKE '%CBO%' AND TA.codigoDoProdutoServico = 1 THEN 'Impresso'
          WHEN PP.descricaoDoPlanoDePagamento NOT LIKE '%CBO%' AND TA.codigoDoProdutoServico = 3 THEN 'Digital'
        END AS Tipo,
        CASE 
          WHEN PP.descricaoDoPlanoDePagamento LIKE '%CBO%' THEN '13'
          ELSE TA.codigoDoProdutoServico
        END AS Produto,
        C.dataDeValidadeInicial AS DataInicio,
        C.dataDevalidadeFinal as DataFinal,
        P.identMF AS CpfCnpj, 
        P.nomeRazaoSocial AS NomeRazaoSocial,
        E.nomeDoMunicipio AS Cidade, E.siglaDaUf AS UF,
        P.diaDeNascimento, P.mesDeNascimento, P.anoDeNascimento, P.sexo,
        C.situacaoDoContrato AS CodSituacao,
        S.descricaoSituacao AS SituacaoContrato, 
        C.dataDaAssinatura AS DataAssinatura, 
        C.valorTotal AS ValorContrato, 
        T.valorDaParcela AS ValorParcela, 
        TA.descricaoTipoDeAssinatura AS TipoAssinatura, PP.descricaoDoPlanoDePagamento AS PlanoPagto
        FROM assContratos C
            INNER JOIN assFinanceiroDoContrato T  WITH (NOLOCK) ON (C.numeroDoContrato = T.numeroDoContrato)
            INNER JOIN assBaixaPagamentos BP      WITH (NOLOCK) ON (T.numeroDoContrato = BP.numeroDoContrato AND T.numeroDaParcela = BP.numeroDaParcela)
            INNER JOIN cadMotivoDeBaixa MB        WITH (NOLOCK) ON (BP.codigoDoMotivoDeBaixa = MB.codigoDoMotivoDeBaixa)
            INNER JOIN cadTipoDeAssinatura TA     WITH (NOLOCK) ON (C.codigoTipoAssinatura = TA.codigoTipoDeAssinatura)
            INNER JOIN cadPlanoDePagamento PP     WITH (NOLOCK) ON (C.codigoDoPlanoDePagamento	= PP.codigoDoPlanoDePagamento)
            INNER JOIN vCadPessoaFisicaJuridica	P WITH (NOLOCK) ON (C.codigoDaPessoa = P.codigoDaPessoa)
            INNER JOIN vEndLogradourosNumeracao	E WITH (NOLOCK) ON (P.codigoDoLogradouro = E.codigoDoLogradouro)
        INNER JOIN cadSituacao S WITH (NOLOCK) ON C.situacaoDoContrato = S.codigoSituacao
        WHERE C.dataDeValidadeInicial <= GETDATE() AND C.dataDevalidadeFinal >= GETDATE()
        AND C.situacaoDoContrato IN (1,3)
        GROUP BY C.numeroDoContrato, descricaoDoPlanoDePagamento, codigoDoProdutoServico, identMF, nomeRazaoSocial, nomeDoMunicipio, siglaDaUf, descricaoSituacao,
          dataDaAssinatura, valorTotal, T.valorDaParcela, situacaoDoContrato, diaDeNascimento, mesDeNascimento, anoDeNascimento, sexo,
          descricaoTipoDeAssinatura, descricaoDoPlanoDePagamento, 
          C.dataDeValidadeInicial, C.dataDevalidadeFinal
        ORDER BY NomeRazaoSocial, DataInicio
      ";

    $stmt = $this->gestor->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function consultaContaDiario(): array
  {
    $sql =
      "SELECT c.name AS Cliente, c.email, c.createdAt, s.endAt, p.name, s.type, s.status
        FROM customers c
        INNER JOIN customer_details cd ON c.id = cd.customerId
        INNER JOIN signatures s ON c.id = s.customerID
        INNER JOIN products p ON s.productId = p.id
        ORDER BY createdAt, c.name
      ";
    $stmt = $this->contaDiario->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
