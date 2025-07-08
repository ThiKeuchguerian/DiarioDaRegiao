<?php
require_once __DIR__ . '/../DBConnect.php';

class ConsultaEstoque
{
  private $senior;

  public function __construct()
  {
    $this->senior = DatabaseConnection::getConnection('senior');
    $this->senior->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
  }

  /**
   * Lista todos os depósitos (CODEMP = 1)
   * @return array
   */
  public function listarDepositos(): array
  {
    $sql  = "SELECT CODDEP, DESDEP FROM e205dep WHERE CODEMP = 1 ORDER BY CODDEP";
    $stmt = $this->senior->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Lista produtos de um depósito
   * @param int $codDep
   * @return array
   */
  public function listarFamlia(string $codDep): array
  {
    $sql  =
      "SELECT  DISTINCT F.CODFAM, F.DESFAM
        FROM E075PRO P
        INNER JOIN e210est M WITH (NOLOCK) ON P.CODEMP = M.CODEMP AND P.CODPRO = M.CODPRO
	      LEFT OUTER JOIN E012FAM F WITH (NOLOCK) ON P.CODEMP = F.CODEMP AND P.CODFAM = F.CODFAM AND P.CODORI = F.CODORI AND M.CODEMP = F.CODEMP
        WHERE M.CODDEP =:codDep
        ORDER BY F.CODFAM
      ";

    $stmt = $this->senior->prepare($sql);
    $stmt->execute([':codDep' => $codDep]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Gera o Período de acordo com mesAno passado
   * @param string $mesAno
   * @return array
   */
  public function gerarPeriodos(string $mesAno): array
  {
    // Quebra MM/yyyy
    list($mes, $ano) = explode('/', $mesAno);
    $mes = (int)$mes;
    $ano = (int)$ano;

    $periodos = [];

    for ($i = 2; $i >= 0; $i--) {
      $data = DateTime::createFromFormat('Y-m', "$ano-" . str_pad($mes, 2, '0', STR_PAD_LEFT));
      $data->modify("-$i month");

      $anoFinal = $data->format('Y');
      $mesFinal = $data->format('m');
      $ultimoDia = $data->format('t');

      $periodos["AnoMes" . (3 - $i)] = $anoFinal . $mesFinal . $ultimoDia;
    }

    return $periodos;
  }
  /**
   * Gera o Período de acordo com mesAno passado
   * @param string $mesAno
   * @return array
   */
  public function geraMesAno(string $mesAno): array
  {
    // Quebra MM/yyyy
    list($mes, $ano) = explode('/', $mesAno);
    $mes = (int)$mes;
    $ano = (int)$ano;

    $periodos = [];

    for ($i = 2; $i >= 0; $i--) {
      $data = DateTime::createFromFormat('m/Y', str_pad($mes, 2, '0', STR_PAD_LEFT) . '/' . $ano);
      $data->modify("-$i month");
      $periodos["mesAno" . (3 - $i)] = $data->format('m/Y');
    }

    return $periodos;
  }
  /**
   * Retorna estoque resumo por depósito para o mês atual e 2 meses atrás
   */
  public function consultaEstoque(string $mesAno, string $codDep, string $codFam): array
  {
    // gera AnoMes1, AnoMes2, AnoMes3
    $p = $this->gerarPeriodos($mesAno);

    // monta dinamicamente as CTEs ConsultaMes1, ConsultaMes2, ConsultaMes3
    $ctes = [];
    for ($i = 1; $i <= 3; $i++) {
      $ctes[] = "
        ConsultaMes{$i} AS (
          SELECT P.CODFAM, F.DESFAM, M.CODPRO, P.DESPRO, M.QTDEST, M.PRMEST, M.VLREST, FORMAT(M.DATMOV,'MM/yyyy') AS MesAno
          FROM E210MVP M
          INNER JOIN E075PRO P ON P.CODEMP=M.CODEMP AND P.CODPRO=M.CODPRO
          INNER JOIN E075DER D ON D.CODEMP=M.CODEMP AND D.CODPRO=M.CODPRO AND D.CODDER=M.CODDER
          INNER JOIN E012FAM F ON F.CODEMP=P.CODEMP AND F.CODFAM=P.CODFAM
          WHERE M.CODEMP = 1
            AND M.FILDEP = 1 
            AND M.ULTMDI = 'S'
            AND P.SITPRO = 'A'
            AND M.CODDEP = :codDep
            AND M.DATMOV <= :mes{$i}
            AND M.ESTMOV IN('NO','NR','NB')
            AND M.DATMOV = (
              SELECT MAX(M2.DATMOV)
              FROM E210MVP M2
              WHERE M2.CODEMP = 1
                AND M2.FILDEP = 1 
                AND M2.ULTMDI = 'S'
                AND M2.ESTMOV IN('NO','NR','NB')
                AND M2.CODDEP = :codDep
                AND M2.CODPRO = M.CODPRO
                AND M2.CODDER = M.CODDER
                AND M2.FILDEP = M.FILDEP
                AND M2.DATMOV <= :mes{$i}
        )";
    }

    $queryFim =
      " SELECT
          COALESCE(C1.CODFAM,C2.CODFAM,C3.CODFAM) AS CODFAM,
          COALESCE(C1.DESFAM,C2.DESFAM,C3.DESFAM) AS DESFAM,
          COALESCE(C1.CODPRO,C2.CODPRO,C3.CODPRO) AS CODPRO,
          COALESCE(C1.DESPRO,C2.DESPRO,C3.DESPRO) AS DESPRO,
          C1.QTDEST AS QTDEST1,
          C1.PRMEST AS PRMEST1,
          C1.VLREST AS VLREST1,
          C2.QTDEST AS QTDEST2,
          C2.PRMEST AS PRMEST2,
          C2.VLREST AS VLREST2,
          C3.QTDEST AS QTDEST3,
          C3.PRMEST AS PRMEST3,
          C3.VLREST AS VLREST3
        FROM ConsultaMes1 C1
        FULL OUTER JOIN ConsultaMes2 C2 ON C1.CODFAM=C2.CODFAM AND C1.CODPRO=C2.CODPRO
        FULL OUTER JOIN ConsultaMes3 C3 ON C1.CODFAM=C3.CODFAM AND C1.CODPRO=C3.CODPRO
        ORDER BY CODFAM,DESFAM,CODPRO,DESPRO 
      ";

    if ($codFam <> '0') {
      $ctes = implode("\n AND F.CODFAM = :codFam ),", $ctes);
      $sql = "WITH " . $ctes . "\n AND F.CODFAM = :codFam ) " . $queryFim;
    } else {
      $sql = "WITH " . implode("),\n", $ctes) . " ) " . $queryFim;
    }

    // Prepara e bind dos parâmetros
    $stmt = $this->senior->prepare($sql);
    if ($codFam <> '0') {
      $params = [
        ':codDep' => $codDep,
        ':codFam' => $codFam
      ];
    } else {
      $params = [':codDep' => $codDep];
    }

    for ($i = 1; $i <= 3; $i++) {
      $params["mes{$i}"] = $p["AnoMes{$i}"];
    }
    // depurar($params, $sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function consultaEstoqueES($dados): array
  {
    $codDep = $dados['Deposito'];
    $codFam = $dados['Familia'];
    $mesAno = $dados['MesAno'];

    // Separa o mês e o ano
    list($mes, $ano) = explode('/', $mesAno);

    // Cria um objeto DateTime a partir do primeiro dia do mês
    $data = DateTime::createFromFormat('Y-m-d', $ano . '-' . $mes . '-01');

    // O primeiro mês é o próprio mês informado
    $mesAno2 = $data->format('m/Y');

    // Subtrai um mês para calcular o mês anterior
    $data->modify('-1 month');
    $mesAno1 = $data->format('m/Y');

    $params = [
      ':CodDep'  => $codDep,
      ':MesAno1' => $mesAno1,
      ':MesAno2' => $mesAno2
    ];

    $sql =
      "SELECT P.CODFAM, F.DESFAM, M.CODPRO, P.DESPRO, M.QTDEST, M.QTDANT, M.QTDMOV, M.VLRMOV, M.ESTEOS, FORMAT(DATMOV, 'MM/yyyy') AS MESMOV 
        FROM e210mvp M
          INNER JOIN E075PRO P ON P.CODEMP = M.CODEMP AND P.CODPRO = M.CODPRO
          INNER JOIN E075DER D ON D.CODEMP = M.CODEMP AND D.CODPRO = M.CODPRO AND D.CODDER = M.CODDER
          INNER JOIN E012FAM F ON F.CODEMP = P.CODEMP AND F.CODFAM = P.CODFAM
        WHERE M.CODEMP = 1 AND M.FILDEP = 1
          AND M.ESTMOV IN ('NO','NR','NB') AND CODDEP = :CodDep
          AND FORMAT(DATMOV, 'MM/yyyy') IN (:MesAno1, :MesAno2)
      ";

    if ($codFam != 0) {
      $sql .= " AND P.CODFAM = :codFam ";
      $params[':codFam'] = $codFam;
    }

    $sql .= " ORDER BY P.CODFAM, M.CODPRO";
    
    $stmt = $this->senior->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
