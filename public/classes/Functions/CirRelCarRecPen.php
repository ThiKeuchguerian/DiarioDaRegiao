<?php
require_once __DIR__ . '/../DBConnect.php';

/**
 * Class CirCartaoRecorrentePendente
 * 
 * Responsible for querying recurring card payment data within a date range.
 */
class CirCartaoRecorrentePendente
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
  public function consultaCartaoRecorrente(string $dtInicio, string $dtFim): array
  {
    $sql =
      " SELECT 
          Con.numeroDoContrato AS Contrato,
          Cad.nomeRazaoSocial AS NomeCompleto,
          AssCC.NumeroDoCartao AS NumCartao,
          AssCC.dataDevalidade AS ValCartao,
          AssCC.codigoDeAcesso AS CodSeg,
          AssFin.dataDoVencimento AS DtVencParc,
          AssFin.saldoValorParcela AS VlrParc,
          P.descricaoDoProdutoServico AS Produto
        FROM assFinanceiroDoContrato AssFin
        INNER JOIN assContratos Con ON AssFin.numeroDoContrato = Con.numeroDoContrato
        INNER JOIN vCadpessoaFisicaJuridica Cad ON Con.codigoDaPessoa = Cad.codigoDaPessoa
        INNER JOIN assCartaoDeCreditoRecorrente AssCC ON AssFin.identificadorCobranca = AssCC.identificadorCobranca
        INNER JOIN assBandeiraDoCartao Ban ON AssCC.codigoDaBandeira = Ban.codigoDaBandeira
        INNER JOIN cadTipoDeAssinatura TipoAss ON Con.codigoTipoAssinatura = TipoAss.codigoTipoDeAssinatura
        INNER JOIN cadProdutosServicos P ON TipoAss.codigoDoProdutoServico = P.codigoDoProdutoServico
        INNER JOIN cadMotivoDeCancelamento MtCan ON Con.codigoDoMotivoDeCancelamento = MtCan.codigoDoMotivoDeCancelamento
        WHERE AssFin.dataDoVencimento >= :dtInicio
          AND AssFin.dataDoVencimento <= :dtFim
          AND AssFin.situacao = 1
        ORDER BY AssFin.dataDoVencimento, Con.numeroDoContrato
      ";

    $stmt = $this->gestor->prepare($sql);
    $stmt->execute([
      ':dtInicio' => $dtInicio,
      ':dtFim' => $dtFim
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
