<?php
require_once __DIR__ . '/../DBConnect.php';

class CirContratosRecebidos
{
  // Conexões
  private $gestor;

  public function __construct()
  {
    $this->gestor = DatabaseConnection::getConnection('gestor');
  }

  public function ConsultaAno(string $ano, string $mesAno): array
  {
    // Datas unívocas YYYYMMDD
    $inicio = $ano . '0101';
    $fim    = $ano . '1231';

    //SQL Base
    $query = "SELECT P.identMF CpfCnpj, --(P.nomeRazaoSocial + '  ('+ LTRIM(RTRIM(P.identMF)) + ')') AS NomeRazaoSocial, 
        P.nomeRazaoSocial AS NomeRazaoSocial,
        E.nomeDoMunicipio AS Cidade, E.siglaDaUf AS UF, 
        C.numeroDoContrato AS Contrato, 
        CASE WHEN C.situacaoDoContrato = 1 THEN 'Ativo' ELSE 'Inativo' END AS ContratoAtivo, 
        C.dataDaAssinatura AS DataAssinatura, 
        FORMAT(C.dataDaAssinatura, 'yyyy') AS AnaDtAss,
        FORMAT(C.dataDaAssinatura, 'MM') AS MesDtAss,
        FORMAT(C.dataDaAssinatura, 'MM/yyyy') AS MesAnoAss,
        C.valorTotal AS ValorContrato, 
        T.numeroDaParcela AS Parcela, 
        T.dataDoVencimento AS  DataVenc, 
        FORMAT(t.dataDoVencimento, 'MM/yyyy') AS MesAnoVenc,
        T.dataDoPagamento AS DataPagto, 
        FORMAT(T.dataDoPagamento, 'yyyy') AS AnoPagto, 
        FORMAT(T.dataDoPagamento, 'MM') AS MesPagto,
        FORMAT(T.dataDoPagamento, 'MM/yyyy') AS MesAnoPagto,
        T.valorDaParcela AS ValorParcela, 
        ISNULL(valorPago, 0) AS ValorPagoParc,
        TA.descricaoTipoDeAssinatura AS TipoAssinatura, PP.descricaoDoPlanoDePagamento AS PlanoPagto, MB.descrDoMotivoDeBaixa AS MotivoBaixaTitulo
      FROM assContratos C
        INNER JOIN assFinanceiroDoContrato T  WITH (NOLOCK) ON (C.numeroDoContrato = T.numeroDoContrato)
        INNER JOIN assBaixaPagamentos BP      WITH (NOLOCK) ON (T.numeroDoContrato = BP.numeroDoContrato AND T.numeroDaParcela = BP.numeroDaParcela)
        INNER JOIN cadMotivoDeBaixa MB        WITH (NOLOCK) ON (BP.codigoDoMotivoDeBaixa = MB.codigoDoMotivoDeBaixa)
        INNER JOIN cadTipoDeAssinatura TA     WITH (NOLOCK) ON (C.codigoTipoAssinatura = TA.codigoTipoDeAssinatura)
        INNER JOIN cadPlanoDePagamento PP     WITH (NOLOCK) ON (C.codigoDoPlanoDePagamento	= PP.codigoDoPlanoDePagamento)
        INNER JOIN vCadPessoaFisicaJuridica	P WITH (NOLOCK) ON (C.codigoDaPessoa = P.codigoDaPessoa)
        INNER JOIN vEndLogradourosNumeracao	E WITH (NOLOCK) ON (P.codigoDoLogradouro = E.codigoDoLogradouro)
    ";

    // Monta dinamicamente o WHERE
    $where  = [];
    if ($ano != '0') {
      $params = [
        ':inicio' => $inicio,
        ':fim'    => $fim
      ];
    }

    // se ano for diferente de zero, filtra por intervalo
    if ($ano != '0') {
      $where[] = "T.dataDoPagamento BETWEEN :inicio AND :fim AND TRIM(MB.descrDoMotivoDeBaixa) = 'RECEBIMENTO'";
    }

    // se mesAno for diferente de zero, filtra por mês/ano
    if ($mesAno !== '') {
      $where[]            = "FORMAT(T.dataDoPagamento,'MM/yyyy') = :mesAno AND TRIM(MB.descrDoMotivoDeBaixa) = 'RECEBIMENTO'";
      $params[':mesAno']  = $mesAno;
    }

    // acrescenta o WHERE se necessário
    if (count($where) > 0) {
      $sql = $query . "\n WHERE " . implode("\n   AND ", $where);
    }

    // Ordenação
    $sql .= "\n ORDER BY P.nomeRazaoSocial, C.dataDaAssinatura";

    // echo "<pre>";
    // var_dump($sql, $params);
    // die();

    $stmt = $this->gestor->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchALl(PDO::FETCH_ASSOC);
  }
}
