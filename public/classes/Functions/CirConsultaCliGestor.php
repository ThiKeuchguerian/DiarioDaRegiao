<?php
require_once __DIR__ . '/../DBConnect.php';

class CirConsultaCliGestor
{
  // Conexões
  private $gestor;

  public function __construct()
  {
    $this->gestor = DatabaseConnection::getConnection('gestor');
  }

  public function consultaAssinante($codAssinante, $numContrato, $emailAssinante)
  {
    $sql =
      "SELECT numeroDoContrato, codigoDoAssinante, nomeRazaoSocial, 
          TRIM(email) AS email, loginDoUsuarioAssinante, senhaDoUsuarioAssinante 
        FROM vAssAssinanturasAtivasWeb
      ";

    $where = [];
    $params = [];

    if ($codAssinante != '') {
      $where[] = 'codigoDoAssinante = :codAssinante';
      $params[':codAssinante'] = $codAssinante;
    }
    if ($numContrato != '') {
      $where[] = 'numeroDoContrato = :numContrato';
      $params[':numContrato'] = $numContrato;
    }
    if ($emailAssinante != '') {
      $where[] = 'email LIKE :emailAssinante';
      $params[':emailAssinante'] = $emailAssinante . '%';
    }

    if (count($where) > 0) {
      $sql .= "\n WHERE " . implode("\n AND ", $where);
    }

    $stmt = $this->gestor->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function consultaContrato($codAssinante, $numContrato, $emailAssinante)
  {
    $sql =
      " SELECT con.numeroDoContrato, con.codigoDaPessoa,
          CASE 
            WHEN cadf.nomeDaPessoa IS NULL THEN cadj.nomeFantasia
            WHEN cadj.nomeFantasia IS NULL THEN cadf.nomeDaPessoa
            else ''
          END AS NomeAssinante,
          CASE
            WHEN con.situacaoDoContrato = '1' THEN 'Ativo'
            WHEN con.situacaoDoContrato = '2' THEN 'Cancelado'
            WHEN con.situacaoDoContrato = '3' THEN 'Suspenso'
            WHEN con.situacaoDoContrato = '4' THEN 'Encerrado'
            WHEN con.situacaoDoContrato = '5' THEN 'Pre Venda'
          END AS situacaoDoContrato,
          CASE 
            WHEN con.tipoDeContrato = 'I' THEN 'Inclusão'
            WHEN con.tipoDeContrato = 'R' THEN 'Renovação'
          END AS tipoDeContrato,
          concat(con.codigoTipoAssinatura, '-', cada.descricaoTipoDeAssinatura) TipoDeAssinatura, trim(cad.email) as email
        FROM cadPessoa cad
        LEFT OUTER JOIN cadPessoaFisica cadf WITH (NOLOCK) ON cad.codigoDaPessoa = cadf.codigoDaPessoa
        LEFT OUTER JOIN cadPessoaJuridica cadj WITH (NOLOCK) ON cad.codigoDaPessoa = cadj.codigoDaPessoa
        INNER JOIN assContratos con WITH (NOLOCK) ON cad.codigoDaPessoa = con.codigoDaPessoa
        INNER JOIN cadTipoDeAssinatura cada WITH (NOLOCK) ON con.codigoTipoAssinatura = cada.codigoTipoDeAssinatura
      ";

    $where = [];
    $params = [];

    if ($codAssinante != '') {
      $where[] = 'con.codigoDaPessoa = :codAssinante';
      $params[':codAssinante'] = $codAssinante;
    }
    if ($numContrato != '') {
      $where[] = 'con.numeroDoContrato = :numContrato';
      $params[':numContrato'] = $numContrato;
    }
    if ($emailAssinante != '') {
      $where[] = 'cad.email LIKE :emailAssinante';
      $params[':emailAssinante'] = $emailAssinante . '%';
    }

    if (count($where) > 0) {
      $sql .= "\n WHERE " . implode("\n AND ", $where) . "\n ORDER BY con.numeroDoContrato DESC";
    }

    $stmt = $this->gestor->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
