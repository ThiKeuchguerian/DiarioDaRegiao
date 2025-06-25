<?php
require_once __DIR__ . '/../DBConnect.php';

class UsuariosGestor
{
  private $gestor;

  public function __construct()
  {
    $this->gestor = DatabaseConnection::getConnection('gestor');
  }

  /**
   * Busca usuários filtra pelo status:
   *   '1' = todos
   *   '2' = inativos (idAtivo = 0)
   *   '3' = ativos   (idAtivo = 1)
   *
   * @param  string|null $status
   * @param  string|null $NomeUser
   * @return array
   */
  public function buscarUsuarios(
    ?string $UserStatus,
    ?string $NomeUser
  ): array {
    // SQL base
    $sql = "SELECT
    CadU.codigoDaPessoa,
    CadU.codigoDoUsuario,
    CASE WHEN CadU.idAtivo = 1 THEN 'Ativo' ELSE 'Inativo' END AS Status,
    COALESCE(CadP.nomeDaPessoa, CadJ.razaoSocial) AS Nome,
    CONVERT(VARCHAR, CadU.dataValidadeSenha, 103) AS dataValidadeSenha
    FROM segUsuario CadU
    LEFT JOIN cadPessoaFisica   CadP ON CadU.codigoDaPessoa = CadP.codigoDaPessoa
    LEFT JOIN cadPessoaJuridica CadJ ON CadU.codigoDaPessoa = CadJ.codigoDaPessoa
  ";

    // monta WHERE e parâmetros
    $where  = [];
    $params = [];
    if ($UserStatus === '2' || $UserStatus === '3') {
      // 2 => inativos, 3 => ativos
      $ativo = $UserStatus === '3' ? 1 : 0;
      $where[]     = 'CadU.idAtivo = :ativo';
      $params[':ativo']  = $ativo;
    }

    // filtro por nome do usuário
    if (!empty($NomeUser)) {
      $where[]               = "COALESCE(CadP.nomeDaPessoa, CadJ.razaoSocial) LIKE :nomeUser";
      $params[':nomeUser']   = $NomeUser . '%';
    }
    
    if (count($where) > 0) {
      $sql .= "\n WHERE " . implode("\n   AND ", $where);
    }

    // ordenação final
    $sql .= "\n ORDER BY Nome";

    // prepara e executa
    $stmt = $this->gestor->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Atualiza a dataValidadeSenha para TODOS os usuários ativos (idAtivo = 1)
   *
   * @param  string $novaDataValidade  deve vir em 'YYYY-MM-DD'
   * @return int  número de linhas afetadas
   */
  public function atualizarDataValidadeSenha(string $novaDataValidade): int
  {
    $sql = " UPDATE segUsuario SET dataValidadeSenha = :novaData WHERE idAtivo = 1 ";
    $stmt = $this->gestor->prepare($sql);
    $stmt->bindValue(':novaData', $novaDataValidade, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->rowCount();
  }

  /**
   * Atualiza a dados do usuário
   * @param  string $Nome
   * @param  string $UserName
   * @param  string $Status
   * @param  string $novaDataValidade  deve vir em 'YYYY-MM-DD'
   * @return int  número de linhas afetadas
   */
  public function atualizarDadosUsuario(
    string $UserName,
    string $Status,
    string $novaDataValidade
  ): int {
    $sql = " UPDATE segUsuario SET dataValidadeSenha = :novaData, idAtivo = :status WHERE codigoDoUsuario = :userName ";

    $stmt = $this->gestor->prepare($sql);
    // echo "<pre>";
    // var_dump($sql);
    // die();
    // bind dos 3 parâmetros
    $stmt->bindValue(':novaData', $novaDataValidade, PDO::PARAM_STR);
    $stmt->bindValue(':status', $Status, PDO::PARAM_INT);
    $stmt->bindValue(':userName', $UserName, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->rowCount();
  }
}
