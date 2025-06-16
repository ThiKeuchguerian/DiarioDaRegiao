<?php
require_once __DIR__ . '/../DBConnect.php';

class CirContratoCboVigDif
{
  // Conexões
  private $gestor;

  public function __construct()
  {
    $this->gestor = DatabaseConnection::getConnection('gestor');
  }

  public function consultaAssinante($numContrato): array
  {
    $sql = "SELECT Con.codigoDaPessoa FROM assContratos Con ";

    $where = [];
    $params = [];

    if ($numContrato != '') {
      $where[] = 'Con.numeroDoContrato = :numContrato';
      $params[':numContrato'] = $numContrato;
    }

    if (count($where) > 0) {
      $sql .= "\n WHERE " . implode("\n AND ", $where);
    }

    $stmt = $this->gestor->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function consultaContrato($codPessoa, $mesAno): array
  {
    $sql =
      " SELECT Con.*, Pl.descricaoDoPlanoDePagamento, Cad.nomeRazaoSocial, 
          Tp.codigoDoProdutoServico, FORMAT(Con.dataDevalidadeFinal, 'MM/yyyy') AS MesAno,
          CAST(Con.dataDeCadastro AS DATE) AS Chave
        FROM assContratos Con
          INNER JOIN cadPlanoDePagamento Pl WITH (NOLOCK) ON Pl.codigoDoPlanoDePagamento = Con.codigoDoPlanoDePagamento
          INNER JOIN vCadPessoaFisicaJuridica Cad WITH(NOLOCK) ON Cad.codigoDaPessoa=Con.codigoDaPessoa
          INNER JOIN cadTipoDeAssinatura Tp WITH (NOLOCK) ON Tp.codigoTipoDeAssinatura = Con.codigoTipoAssinatura
        WHERE Con.situacaoDoContrato = 1 and Pl.descricaoDoPlanoDePagamento LIKE '%CBO%' --AND Con.numeroDoContrato <> '582812'
      ";

    $where = [];
    $params = [];

    if ($codPessoa != '') {
      $where[] = "con.codigoDaPessoa = :codPessoa";
      $params[':codPessoa'] = $codPessoa;
    }
    if ($mesAno != '') {
      $where[] = "FORMAT(Con.dataDevalidadeFinal, 'MM/yyyy') = :mesAno";
      $params[':mesAno'] = $mesAno;
    }

    if (count($where) > 0) {
      $sql .= "\n AND " . implode("\n AND ", $where);
    }
    $sql .= "\n ORDER BY Con.codigoDaPessoa, Con.numeroDoContrato ";

    $stmt = $this->gestor->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function consultaAnalitico($codAssinante): array
  {
    $sql =
      " SELECT Con.*, Pl.descricaoDoPlanoDePagamento, Cad.nomeRazaoSocial, 
          Tp.codigoDoProdutoServico, FORMAT(Con.dataDevalidadeFinal, 'MM/yyyy') AS MesAno,
          CAST(Con.dataDeCadastro AS DATE) AS Chave,
          CASE
            WHEN Con.situacaoDoContrato = '1' THEN 'Ativo'
            WHEN Con.situacaoDoContrato = '2' THEN 'Cancelado'
            WHEN Con.situacaoDoContrato = '3' THEN 'Suspenso'
            WHEN Con.situacaoDoContrato = '4' THEN 'Encerrado'
            WHEN Con.situacaoDoContrato = '5' THEN 'Pre-Venda'
          END AS sitContrato 
        FROM assContratos Con
          INNER JOIN cadPlanoDePagamento Pl WITH (NOLOCK) ON Pl.codigoDoPlanoDePagamento = Con.codigoDoPlanoDePagamento
          INNER JOIN vCadPessoaFisicaJuridica Cad WITH(NOLOCK) ON Cad.codigoDaPessoa=Con.codigoDaPessoa
          INNER JOIN cadTipoDeAssinatura Tp WITH (NOLOCK) ON Tp.codigoTipoDeAssinatura = Con.codigoTipoAssinatura
        WHERE Pl.descricaoDoPlanoDePagamento LIKE '%CBO%'
      ";

    $where = [];
    $params = [];

    if ($codAssinante != '') {
      $where[] = "con.codigoDaPessoa = :codPessoa";
      $params[':codPessoa'] = $codAssinante;
    }

    if (count($where) > 0) {
      $sql .= "\n AND " . implode("\n AND ", $where);
    }
    $sql .= "\n ORDER BY Con.numeroDoContrato ";

    $stmt = $this->gestor->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Atualiza contratos de produto=3 usando as datas do contrato de produto=1
   *
   * @param PDO   $conn
   * @param array $dados  deve conter as chaves:
   *                      'contrato'        => array de contratos,
   *                      'produto'         => array de produtos,
   *                      'dtinicial'       => array de datas início,
   *                      'dtfinal'         => array de datas fim,
   *                      'codigoDaPessoa'  => string
   * @return int número total de contratos atualizados
   */
  public function updateContrato(array $dados): int
  {
    // Extrai do array
    $contrato   = $dados['contrato'];
    $produto    = $dados['produto'];
    $dtVinicial = $dados['dtInicio'];
    $dtVfinal   = $dados['dtFinal'];
    $codPessoa  = $dados['codigoDaPessoa'];
    $mesAno     = $dados['MesAno'];

    // Identifica as datas do produto=1 e colhe contratos produto=3
    $dataInicio1 = null;
    $dataFim1    = null;
    $toUpdate    = [];

    foreach ($contrato as $i => $numContrato) {
      $prd = trim((string)($produto[$i] ?? ''));
      if ($prd === '1') {
        $dataInicio1 = $dtVinicial[$i] ?? null;
        $dataFim1    = $dtVfinal[$i]   ?? null;
      } elseif ($prd === '3') {
        $toUpdate[] = $numContrato;
      }
    }

    if (!$dataInicio1 || !$dataFim1 || empty($toUpdate) || !$codPessoa) {
      return 0;
    }

    // Prepara o UPDATE
    $sql  = "UPDATE assContratos
               SET dataDeValidadeInicial = ?,
                   dataDevalidadeFinal   = ?
             WHERE codigoDaPessoa      = ?
               AND numeroDoContrato     = ?";
    $stmt = $this->gestor->prepare($sql);

    // Executa para cada contrato com produto igual 3
    $count = 0;
    foreach ($toUpdate as $numContrato) {
      if ($stmt->execute([
        $dataInicio1,
        $dataFim1,
        $codPessoa,
        $numContrato
      ])) {
        $count += $stmt->rowCount();
      }
    }

    return $count;
  }
}
