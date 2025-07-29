<?php
require_once __DIR__ . '/../DBConnect.php';

class GraficaProducao
{
  private $gdc;
  private $senior;

  public function __construct()
  {
    $this->gdc = DatabaseConnection::getConnection('gdcTeste');
    $this->senior = DatabaseConnection::getConnection('senior');
  }

  public function consultaFamilia(): array
  {
    $sql =
      "SELECT F.CODFAM, F.DESFAM
        FROM E012FAM F
        WHERE F.CODFAM IN ('01005', '01006') AND F.CODEMP = '1'
        ORDER BY F.CODFAM
      ";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function consultaProduto(array $dados): array
  {
    $codEmp = '1';
    $codFam = isset($dados['codFam']) ? trim($dados['codFam']) : '';

    $sql =
      "SELECT P.CODPRO, P.DESPRO 
        FROM E075PRO P
      ";

    $where = [];
    $params = [];

    if (!empty($codFam)) {
      $where[] = "P.codfam = :codFam";
      $params[':codFam'] = $codFam;
    } else {
      $where[] = "P.codfam IN ('01005', '01006')";
    }

    // Sempre aplica o filtro por codemp
    $where[] = "P.codemp = :codEmp";
    $params[':codEmp'] = $codEmp;


    if (!empty($where)) {
      $sql .= "\n WHERE " . implode(' AND ', $where);
    }

    $sql .= "\n ORDER BY P.codfam";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function consultaProducao(array $dados): array
  {
    $dtInicio = $dados['dtInicio'];
    $dtFim = $dados['dtFim'];
    $caderno = $dados['caderno'];

    $where  = [];
    $params = [];

    $sql =
      "SELECT ID, DataProducao, Caderno, Papel, Gramatura, QtdeChapa, TrocaBobina, 
          QuebraPapel, DefeitoChapa, Maquina, TiragemLiq, TiragemBru, TiragemDif, 
          HoraInicio, HoraFim, Duracao, Kilo, NumeroOP, Obs
        FROM GraficaProducao
      ";

    if (!empty($dtInicio) && !empty($dtFim)) {
      $where[] = "DataProducao BETWEEN :dtInicio AND :dtFim";
      $params[':dtInicio'] = $dtInicio;
      $params[':dtFim'] = $dtFim;
    }
    if (!empty($dtInicio) && empty($dtFim)) {
      $where[] = "DataProducao = :dtInicio";
      $params[':dtInicio'] = $dtInicio;
    }

    if (!empty($caderno)) {
      $where[] = "Caderno LIKE :caderno";
      $params[':caderno'] = $caderno . '%';
    }

    if (count($where) > 0) {
      $sql .= "\n WHERE " . implode(' AND ', $where);
    }

    $sql .= "\n ORDER BY DataProducao, Papel, Gramatura, Caderno";

    // depurar($sql, $dtInicio);
    $stmt = $this->gdc->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function incluirProducao(array $dados): bool
  {
    $id = $dados['ID'];
    $dtProd = $dados['DataProducao'];
    $caderno = $dados['Caderno'];
    $papel = $dados['Papel'];
    $gramatura = $dados['Gramatura'];
    $qtdeChapa = $dados['QtdeChapa'];
    $trocaBobina = $dados['TrocaBobina'];
    $quebraPapel = $dados['QuebraPapel'];
    $defeitoChapa = $dados['DefeitoChapa'];
    $maquina = $dados['Maquina'];
    $tiragemLiq = $dados['TiragemLiquida'];
    $tiragemBru = $dados['TiragemBruta'];
    $tiragemDif = $tiragemBru - $tiragemLiq;
    $hrInicio = $dados['HoraInicio'];
    $hrFim = $dados['HoraFim'];

    $hrInicioCalculo = new DateTime($hrInicio);
    $hrFimCalculo = new DateTime($hrFim);
    if ($hrFimCalculo < $hrInicioCalculo) {
      $hrFimCalculo->modify('+1 day');
    }
    $duracao = $hrFimCalculo->diff($hrInicioCalculo)->format('%h:%i');
    $vlrL = 0;
    $vlrG = 0;
    // Usando expressões regulares para capturar os valores após "L" e "G"
    if (preg_match('/L(\d+)\s*G(\d+)/', $gramatura, $matches)) {
      $vlrL = $matches[1] / 1000; // O valor após L
      $vlrG = $matches[2] / 1000; // O valor após G
    }
    // Calcula o valor da máquina
    if ($maquina === 'C150') {
      $vlrMaquina = 0.546;
    } elseif ($maquina === 'Nebiolo') {
      $vlrMaquina = 0.578;
    }
    // Calcula o valor do Kilo
    $kilo = intval($vlrL * $vlrG * $vlrMaquina * $tiragemBru);
    $obs = $dados['Obs'];

    if (isset($id)) {
      try {
        $insert =
          "INSERT INTO GraficaProducao 
            (DataProducao, Caderno, Papel, Gramatura, QtdeChapa, TrocaBobina, QuebraPapel, DefeitoChapa, 
            Maquina, TiragemLiq, TiragemBru, TiragemDif, HoraInicio, HoraFim, Duracao, Kilo, Obs)
          VALUES (:dtProd, :caderno, :papel, :gramatura, :qtdeChapa, :trocaBobina, :quebraPapel, :defeitoChapa, 
            :maquina, :tiragemLiq, :tiragemBru, :tiragemDif, :hrInicio, :hrFim, :duracao, :kilo, :obs)
        ";
        $stmt = $this->gdc->prepare($insert);
        $stmt->execute([
          ':dtProd'       => $dtProd,
          ':caderno'      => $caderno,
          ':papel'        => $papel,
          ':gramatura'    => $gramatura,
          ':qtdeChapa'    => $qtdeChapa,
          ':trocaBobina'  => $trocaBobina,
          ':quebraPapel'  => $quebraPapel,
          ':defeitoChapa' => $defeitoChapa,
          ':maquina'      => $maquina,
          ':tiragemLiq'   => $tiragemLiq,
          ':tiragemBru'   => $tiragemBru,
          ':tiragemDif'   => $tiragemDif,
          ':hrInicio'     => $hrInicio,
          ':hrFim'        => $hrFim,
          ':duracao'      => $duracao,
          ':kilo'         => $kilo,
          ':obs'          => $obs
        ]);

        return $stmt->rowCount() > 0;
      } catch (PDOException $e) {
        // Opcional: log do erro
        error_log("Erro ao incluir produção: " . $e->getMessage());
        return false;
      }
    }
    return false;
  }

  public function editarProducao(array $dados): bool
  {
    $id = $dados['ID'];
    $dtProd = $dados['DataProducao'];
    $caderno = $dados['Caderno'];
    $papel = $dados['Papel'];
    $gramatura = $dados['Gramatura'];
    $qtdeChapa = $dados['QtdeChapa'];
    $trocaBobina = $dados['TrocaBobina'];
    $quebraPapel = $dados['QuebraPapel'];
    $defeitoChapa = $dados['DefeitoChapa'];
    $maquina = $dados['Maquina'];
    $tiragemLiq = $dados['TiragemLiquida'];
    $tiragemBru = $dados['TiragemBruta'];
    $tiragemDif = $tiragemBru - $tiragemLiq;
    $hrInicio = $dados['HoraInicio'];
    $hrFim = $dados['HoraFim'];
    $numOp = $dados['NumeroOP'];
    $obs = $dados['Obs'];

    // Calcula tempo de produção
    $hrInicioCalculo = new DateTime($hrInicio);
    $hrFimCalculo = new DateTime($hrFim);
    if ($hrFimCalculo < $hrInicioCalculo) {
      $hrFimCalculo->modify('+1 day');
    }
    $duracao = $hrFimCalculo->diff($hrInicioCalculo)->format('%h:%i');

    // Formata a Data de Produção
    if ($dtProd !== '') {
      $dt = DateTime::createFromFormat('d/m/Y', $dtProd);
      $dtProd = $dt->format('Y-m-d');
    } else {
      $dtProd = '';
    }

    // Usando expressões regulares para capturar os valores após "L" e "G"
    $vlrL = 0;
    $vlrG = 0;
    if (preg_match('/L(\d+)\s*G(\d+)/', $gramatura, $matches)) {
      $vlrL = $matches[1] / 1000; // O valor após L
      $vlrG = $matches[2] / 1000; // O valor após G
    }

    // Calcula o valor da máquina
    if ($maquina === 'C150') {
      $vlrMaquina = 0.546;
    } elseif ($maquina === 'Nebiolo') {
      $vlrMaquina = 0.578;
    }

    // Calcula o valor do Kilo
    $kilo = intval($vlrL * $vlrG * $vlrMaquina * $tiragemBru);

    if ($id !== '') {
      $dtLog = date('Y-m-d H:i:s');
      $insertLog =
        "INSERT INTO GraficaProducaoLogs
            (ID, DataProducao, Caderno, Papel, Gramatura, QtdeChapa, TrocaBobina, QuebraPapel, DefeitoChapa, 
            Maquina, TiragemLiq, TiragemBru, TiragemDif, HoraInicio, HoraFim, Duracao, Kilo, NumeroOP, Obs, DataLog, Situacao)
          VALUES (:id, :dtProd, :caderno, :papel, :gramatura, :qtdeChapa, :trocaBobina, :quebraPapel, :defeitoChapa, 
            :maquina, :tiragemLiq, :tiragemBru, :tiragemDif, :hrInicio, :hrFim, :duracao, :kilo, :numeroOP, :obs, :dtLog, :situacao)
        ";

      $stmtLog = $this->gdc->prepare($insertLog);
      $stmtLog->execute([
        ':id'           => $id,
        ':dtProd'       => $dtProd,
        ':caderno'      => $caderno,
        ':papel'        => $papel,
        ':gramatura'    => $gramatura,
        ':qtdeChapa'    => $qtdeChapa,
        ':trocaBobina'  => $trocaBobina,
        ':quebraPapel'  => $quebraPapel,
        ':defeitoChapa' => $defeitoChapa,
        ':maquina'      => $maquina,
        ':tiragemLiq'   => $tiragemLiq,
        ':tiragemBru'   => $tiragemBru,
        ':tiragemDif'   => $tiragemDif,
        ':hrInicio'     => $hrInicio,
        ':hrFim'        => $hrFim,
        ':duracao'      => $duracao,
        ':kilo'         => $kilo,
        ':numeroOP'     => $numOp,
        ':obs'          => $obs,
        ':dtLog'        => $dtLog,
        ':situacao'     => 'Alterado'
      ]);

      $sqlUp =
        "UPDATE GraficaProducao 
          SET 
            DataProducao = :dtProd, Caderno = :caderno, Papel = :papel, Gramatura = :gramatura, QtdeChapa = :qtdeChapa, TrocaBobina = :trocaBobina,
            QuebraPapel = :quebraPapel, DefeitoChapa = :defeitoChapa, Maquina = :maquina, TiragemLiq = :tiragemLiq, TiragemBru = :tiragemBru, 
            TiragemDif = :tiragemDif, HoraInicio = :hrInicio, HoraFim = :hrFim, Duracao = :duracao, Kilo = :kilo, NumeroOP = :numeroOP, Obs = :obs
          WHERE ID = :id
        ";
      $stmt = $this->gdc->prepare($sqlUp);
      $stmt->execute([
        ':id'           => $id,
        ':dtProd'       => $dtProd,
        ':caderno'      => $caderno,
        ':papel'        => $papel,
        ':gramatura'    => $gramatura,
        ':qtdeChapa'    => $qtdeChapa,
        ':trocaBobina'  => $trocaBobina,
        ':quebraPapel'  => $quebraPapel,
        ':defeitoChapa' => $defeitoChapa,
        ':maquina'      => $maquina,
        ':tiragemLiq'   => $tiragemLiq,
        ':tiragemBru'   => $tiragemBru,
        ':tiragemDif'   => $tiragemDif,
        ':hrInicio'     => $hrInicio,
        ':hrFim'        => $hrFim,
        ':duracao'      => $duracao,
        ':kilo'         => $kilo,
        ':numeroOP'     => $numOp,
        ':obs'          => $obs
      ]);
    }
    if ($stmt->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  }

  public function excluirProducao(array $dados)
  {
    $id = $dados['ID'];

    if ($id !== '') {
      $dtLog = date('Y-m-d H:i:s');
      $insertLog =
        "INSERT INTO GraficaProducaoLogs
            (ID, DataProducao, Caderno, Papel, Gramatura, QtdeChapa, TrocaBobina, QuebraPapel, DefeitoChapa, 
            Maquina, TiragemLiq, TiragemBru, TiragemDif, HoraInicio, HoraFim, Duracao, Kilo, NumeroOP, Obs, DataLog, Situacao)
          SELECT ID, DataProducao, Caderno, Papel, Gramatura, QtdeChapa, TrocaBobina, QuebraPapel, DefeitoChapa,
            Maquina, TiragemLiq, TiragemBru, TiragemDif, HoraInicio, HoraFim, Duracao, Kilo, NumeroOP, Obs, :dtLog, :situacao
          FROM GraficaProducao
          WHERE ID = :id
        ";

      $stmtLog = $this->gdc->prepare($insertLog);
      $stmtLog->execute([
        ':id'           => $id,
        ':dtLog'        => $dtLog,
        ':situacao'     => 'Excluído'
      ]);

      $sqlDel =
        "DELETE FROM GraficaProducao WHERE ID = $id
        ";

      $stmt = $this->gdc->prepare($sqlDel);
      $stmt->execute();
    }
    if ($stmt->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  }

  public function verifica(array $dados): bool
  {
    $familia = $dados['Papel'];
    $produto = $dados['Gramatura'];

    // Separa a string em duas partes e remove espaços extras se necessário
    $partes = explode(' - ', $produto, 2);
    $codProd   = trim($partes[0] ?? '');
    $descProd = trim($partes[1] ?? '');

    if (substr($codProd, 0, 5) !== $familia) {
      return false;
    } else {
      return true;
    }
  }
}
