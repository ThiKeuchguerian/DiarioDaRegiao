<?php
require_once __DIR__ . '/../DBConnect.php';

class ValidadorCpfl
{
  private $gestor;

  public function __construct()
  {
    $this->gestor = DatabaseConnection::getConnection('gestor');
  }

  /**
   * Consulta se arquivo existe
   *
   * @param string|null $nomeArquivo'
   * @return int
   */
  public function consultaArquivo(string $nomeArquivo): int
  {
    $sql = "SELECT COUNT(*) as total FROM DR_RETCPFL WHERE nomeDoArquivo = :nomeArquivo";

    $stmt = $this->gestor->prepare($sql);
    $stmt->execute([':nomeArquivo' => $nomeArquivo]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int) ($result['total'] ?? 0);
  }

  public function processaArq(string $nomeOriginal, string $arqOri)
  {

    $linhas = file($nomeOriginal, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $sequencia = 1;
    $dtGeracao = substr($nomeOriginal, -19, 8);
    $hrGeracao = substr($nomeOriginal, -10, 6);
    $arqOri = trim($arqOri);

    $insert =
      "INSERT INTO DR_RETCPFL 
        (sequenciaArquivo, tipo, tituloDaParcela, parcela, numBancario, valorRecebido, 
          vctCtaEnergia, pgtDaCtaEnergia, dtGeracao, hrGeracao, unidadeConsumidora, reg, nomeDoArquivo)
        VALUES (:seq, :tipo, :con, :parc, :nban, :vrec, :vcta, :pcta, :dtge, :hrge, :ucon, :reg, :narq)
      ";

    foreach ($linhas as $linha) {
      $tipo = substr($linha, 0, 1);
      switch ($tipo) {
        case '5':
          $contrato = substr($linha, -139, 6);
          $parcela  = substr($linha, -133, 2);
          $numBanc  = substr($linha, -131, 12);
          $vlrRec   = (substr($linha, -110, 6)) / 100;
          $vctCta   = substr($linha, -98,  8);
          $pgtCta   = substr($linha, -90,  8);
          $unCon    = substr($linha, -149, 10);
          $reg      = substr($linha, -150, 150);

          //depurar($sequencia++, $tipo, $contrato, $parcela, $numBanc, $vlrCor, $vctCta, $pgtCta, $dtGeracao, $hrGeracao, $unCon, $reg, $arqOri);

          $stmt = $this->gestor->prepare($insert);
          $stmt->execute([
            ':seq'  => $sequencia++,
            ':tipo' => $tipo,
            ':con'  => $contrato,
            ':parc' => $parcela,
            ':nban' => $numBanc,
            ':vrec' => $vlrRec,
            ':vcta' => $vctCta,
            ':pcta' => $pgtCta,
            ':dtge' => $dtGeracao,
            ':hrge' => $hrGeracao,
            ':ucon' => $unCon,
            ':reg'  => $reg,
            ':narq' => $arqOri
          ]);
          break;
        default:
          $reg = substr($linha, -150, 150);
          // depurar($sequencia++, $tipo, $dtGeracao, $hrGeracao, $reg, $arqOri);
          $stmt = $this->gestor->prepare($insert);
          $stmt->execute([
            ':seq'  => $sequencia++,
            ':tipo' => $tipo,
            ':con'  => '',
            ':parc' => '',
            ':nban' => '',
            ':vrec' => '',
            ':vcta' => '',
            ':pcta' => '',
            ':dtge' => $dtGeracao,
            ':hrge' => $hrGeracao,
            ':ucon' => '',
            ':reg'  => $reg,
            ':narq' => $arqOri
          ]);
          break;
      }
    }
  }
  /**
   * Consulta recebimentos CPFL e saldo no Gestor
   *
   * @param int|null    $pendentes  null=todos, 1=recebidos, 2=pendentes
   * @param string|null $dtInicio   'YYYY-MM-DD'
   * @param string|null $dtFim      'YYYY-MM-DD'
   * @return array
   */
  public function consultaCPFL(
    ?int    $pendentes   = null,
    ?string $dtInicio    = null,
    ?string $dtFim       = null,
    ?string $nomeArquivo = null
  ): array {
    // Converter as datas para o formato YYYYMMDD
    $dtInicio = str_replace('-', '', $dtInicio);
    $dtFim    = str_replace('-', '', $dtFim);

    $sql =
      " SELECT
          CPFL.unidadeConsumidora as UC,
          CPFL.tituloDaParcela as Titulo,
          CPFL.parcela AS Parcela,
          CPFL.valorRecebido AS valorRecebidoCPFL,
          AssFin.valorDaParcela AS ValorDaParcelaGESTOR,
          AssFin.saldoValorParcela AS SaldoTituloGESTOR,
          CPFL.nomeDoArquivo,
          CPFL.tipo,
          CONVERT(VARCHAR(10), CONVERT(DATE, STUFF(STUFF(CPFL.dtGeracao, 5, 0, '-'), 8, 0, '-')), 103) AS  dtGeracao,
          CONVERT(VARCHAR(8), CONVERT(TIME, STUFF(STUFF(CPFL.hrGeracao, 3, 0, ':'), 6, 0, ':')), 108) AS hrGeracao
        FROM assFinanceiroDoContrato AssFin
        RIGHT JOIN DR_RETCPFL CPFL
          ON AssFin.numeroDoContrato = CPFL.tituloDaParcela
        AND AssFin.numeroDaParcela = CPFL.parcela
      ";

    // monta condições
    $where  = [];
    $params = [];

    // Filtro por nome do arquivo
    if($nomeArquivo){
      $where[]            = 'CPFL.nomeDoArquivo = :nomeArq';
      $params[':nomeArq'] = $nomeArquivo;
    }
    // filtro por intervalo de geração
    if ($dtInicio && $dtFim) {
      $where[]           = 'CPFL.dtGeracao BETWEEN :di AND :df';
      $params[':di']     = $dtInicio . ' 00:00:00';
      $params[':df']     = $dtFim    . ' 23:59:59';
    } elseif ($dtInicio) {
      $where[]           = 'CPFL.dtGeracao = :di';
      $params[':di']     = $dtInicio . ' 00:00:00';
    }

    // sempre filtra tipo = 5
    $where[] = "CPFL.tipo = '5'";

    // se pendentes = 2, filtra saldo > 0
    if ($pendentes === 2) {
      $where[] = 'AssFin.saldoValorParcela > 0';
    }
    // se pendentes = 1, nada a mais (já pega todos com tipo=5)

    // adiciona WHERE ao SQL
    if (count($where)) {
      $sql .= "\nWHERE " . implode("\n  AND ", $where);
    }

    // ordenação
    $sql .= "\n ORDER BY CPFL.dtGeracao, CPFL.hrGeracao, CPFL.tituloDaParcela, CPFL.parcela";

    // prepara e executa
    $stmt = $this->gestor->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
