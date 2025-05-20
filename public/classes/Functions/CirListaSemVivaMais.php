<?php
require_once __DIR__ . '/../DBConnect.php';

class CirListaSemVivaMais
{
  // Conexões
  private $gestor;

  public function __construct()
  {
    $this->gestor = DatabaseConnection::getConnection('gestor');
  }

  public function ConsultaContratos()
  {
    $query = "SELECT DISTINCT 
      CASE 
        WHEN dbo.vCadPessoaFisicaJuridica.idTipoDePessoa=1 THEN 'Fisica'
        WHEN dbo.vCadPessoaFisicaJuridica.idTipoDePessoa=2 THEN 'Juridica'
      END AS 'Tipo', dbo.assContratos.numeroDoContrato AS Contrato, dbo.vCadPessoaFisicaJuridica.nomeRazaoSocial AS NomeAssinante, 
      CONCAT (LTRIM(RTRIM(dbo.vCadEnderecoDeEntrega.siglaTipoLogradouro)),'. ', 
        LTRIM(RTRIM(dbo.vCadEnderecoDeEntrega.nomeDoLogradouro)), ', ', 
        LTRIM(RTRIM(dbo.vCadEnderecoDeEntrega.numeroDoEndereco)), ' - ', 
        LTRIM(RTRIM(dbo.vCadEnderecoDeEntrega.nomeDoBairro))) as Endereco,
      dbo.vCadEnderecoDeEntrega.nomeDoMunicipio AS Municipio,
      dbo.disSetorDeEntrega.nomeDoSetorDeEntrega AS Setor
      FROM dbo.assContratos 
        INNER JOIN dbo.vCadPessoaFisicaJuridica ON dbo.assContratos.codigoDaPessoa = dbo.vCadPessoaFisicaJuridica.codigoDaPessoa 
        INNER JOIN dbo.assLocalDeEntregaContrato ON dbo.assContratos.numeroDoContrato = dbo.assLocalDeEntregaContrato.numeroDoContrato 
        INNER JOIN dbo.vCadEnderecoDeEntrega ON dbo.assLocalDeEntregaContrato.codigoDoEnderecamento = dbo.vCadEnderecoDeEntrega.codigoDoEnderecamento 
        INNER JOIN dbo.disSetorDeEntrega ON dbo.vCadEnderecoDeEntrega.codigoDoSetorDeEntrega = dbo.disSetorDeEntrega.codigoDoSetorDeEntrega
      WHERE  dbo.disSetorDeEntrega.codigoDoSetorDeEntrega in (7,11,22,23,43,51,53,55,57,63,65,309,334)  /*AND dbo.vCadPessoaFisicaJuridica.idTipoDePessoa = 1*/ AND
      (NOT (dbo.assContratos.codigoDaPessoa IN
        (SELECT codigoDaPessoa FROM dbo.assContratos AS assContratos_1 
          WHERE (codigoTipoAssinatura IN (41, 42, 43))))) AND (dbo.assContratos.situacaoDoContrato = 1) AND (dbo.assContratos.codigoTipoAssinatura IN
            (SELECT codigoTipoDeAssinatura FROM dbo.cadTipoDeAssinatura
              WHERE   (codigoDoProdutoServico = 1))) AND (dbo.assContratos.codigoDoPlanoDePagamento IN 
                (SELECT codigoDoPlanoDePagamento FROM dbo.cadPlanoDePagamentoDiasDeEntrega
                  WHERE   (textoDiaDeEntrega = 'SNSSSNS')))
      ORDER BY Endereco, NomeAssinante, Setor
    ";

    $stmt = $this->gestor->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
