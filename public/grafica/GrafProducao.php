<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/GrafProducao.php';

$Titulo = 'Gráfica - Produção';
$URL = URL_PRINCIPAL . 'grafica/GrafProducao.php';

// Instanciar a classe
$GraficaProducao = new GraficaProducao();

// Iniciando seção
session_start();

// Verifica se a requisição é AJAX
if (isset($_GET['action']) && $_GET['action'] === 'getProdutos') {
  if (isset($_GET['codfam']) && !empty($_GET['codfam'])) {
    $codFam = $_GET['codfam'];
    $dados = ['codFam' => $codFam];

    // Supondo que $GraficaProducao já está instanciado corretamente
    $consultaProduto = $GraficaProducao->consultaProduto($dados);

    // Retorna os dados no formato JSON
    header('Content-Type: application/json');
    echo json_encode($consultaProduto);
    exit; // Finaliza o script

  } else {
    // Se codfam não foi passado ou está vazio
    http_response_code(400); // Código HTTP 400 - Bad Request
    echo json_encode(['erro' => 'Parâmetro codfam é obrigatório.']);
    exit;
  }
}

$buscaFamilia = $GraficaProducao->consultaFamilia();
$dados = [];
$buscaProduto = $GraficaProducao->consultaProduto($dados);
$executaConsulta = false;

if (isset($_POST['btn-buscar'])) {
  $dados = $_POST;

  $_SESSION['dtInicio'] = $dados['dtInicio'];
  $_SESSION['dtFim'] = $dados['dtFim'];

  $executaConsulta = true;
} else if (isset($_POST['BtnIncluirModal'])) {
  $dados = $_POST;

  $incluir = $GraficaProducao->incluirProducao($dados);

  if ($incluir) {
    $dados = [
      'dtInicio'   => $dados['DataProducao'] ?? '', // se não existir, usa ''
      'dtFim'      => '',
      'caderno'    => '',
      'btn-buscar' => ''
    ];
    // depurar($dados);
    $executaConsulta = true;
  } else {
    // Se falhou, exibe alerta usando JavaScript
    echo "alert('Erro ao incluir a produção!');";
  }
} else if (isset($_POST['BtnSalvarModal'])) {
  $dados = $_POST;

  $verifica = $GraficaProducao->verifica($dados);
  if ($verifica) {
    $editarProducao = $GraficaProducao->editarProducao($dados);
  } else {
    echo
    "alert('Produto não pertence a essa Família!');";
    exit;
  }

  if (!empty($dados['DataProducao'])) {
    $dados = [
      'dtInicio' => date('Y-m-d', strtotime(str_replace('/', '-', $dados['DataProducao']))) ?? '',
      'dtFim'      => '',
      'caderno'    => ''
    ];
    $executaConsulta = true;
  } else {
    echo "alert('Erro ao incluir a produção!');";
  }
} else if (isset($_POST['BtnExcluirModal'])) {
  $dados = $_POST;
  $verifica = $GraficaProducao->verifica($dados);

  if ($verifica) {
    $excluirProducao = $GraficaProducao->excluirProducao($dados);
  } else {
    echo
    "alert('Produto não pertence a essa Família!');";
    exit;
  }

  if (!empty($dados['DataProducao'])) {
    $dados = [
      'dtInicio' => date('Y-m-d', strtotime(str_replace('/', '-', $dados['DataProducao']))) ?? '',
      'dtFim'      => '',
      'caderno'    => ''
    ];
    $executaConsulta = true;
    $dadosConsulta = $dados;
  } else {
    // Se falhou, exibe alerta usando JavaScript
    echo "alert('Erro ao excluir a produção!');";
  }
}

if ($executaConsulta) {
  $buscaProducao = $GraficaProducao->consultaProducao($dados);
  $Total = count($buscaProducao);
  // depurar($executaConsulta, $dados);
  $dadosAgrupados = array();
  foreach ($buscaProducao as $item) {
    $dtProd = $item['DataProducao'];
    if (!isset($dadosAgrupados[$dtProd])) {
      $dadosAgrupados[$dtProd] = array();
    }
    $dadosAgrupados[$dtProd][] = $item;
  }
}

// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center filter-fields">
  <div class="col col-sm-6">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="form" name="form">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Dt. Inicial</strong>
            </div>
            <div class="col">
              <strong>Dt. Final</strong>
            </div>
            <div class="col">
              <strong>Caderno</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="date" class="form-control form-control-sm" id="dtInicio" name="dtInicio">
            </div>
            <div class="col">
              <input type="date" class="form-control form-control-sm" id="dtFim" name="dtFim">
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="caderno" name="caderno">
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            <button id="btn-incluir" name="btn-incluir" type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#IncluirModal">Incluir</button>
            <a class="btn btn-primary btn-sm" href="<?= URL_PRINCIPAL ?>">Voltar</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<!-- Exibindo Resultado Buscar -->
<?php if (!empty($Total)) : ?>
  <div class="container">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <?php foreach ($dadosAgrupados as $semaanaAno => $dadosSemana) : ?>
          <h5 class="card-header bg-primary text-white">
            Qtde. Total: <?= count($dadosSemana) ?>
          </h5>
          <table class="table table-striped table-hover mb-0" id="ProducaoGrafica" name="ProducaoGrafica">
            <thead>
              <tr class="table-primary">
                <th scope="col" colspan="9"></th>
                <th scope="col" colspan="3" style="text-align: center;">Tiragem</th>
                <th scope="col" colspan="2" style="text-align: center;">Hora</th>
                <th scope="col" colspan="5"></th>
              </tr>
              <tr class="table-primary">
                <th scope="col">Data Produção</th>
                <th scope="col">Caderno</th>
                <th scope="col">Papel</th>
                <th scope="col">Qtde. Chapa</th>
                <th scope="col">Trocou Bobina ?</th>
                <th scope="col">Quebrou Papel ?</th>
                <th scope="col">Defeito Chapa ?</th>
                <th scope="col">Maquina</th>
                <th scope="col">Liq.</th>
                <th scope="col">Bru.</th>
                <th scope="col">Diferença</th>
                <th scope="col">Inicio</th>
                <th scope="col">Fim</th>
                <th scope="col">Duração</th>
                <th scope="col">Kilo</th>
                <th scope="col">Num.OP</th>
                <th scope="col">Obs.</th>
                <th scope="col" colspan="2">Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($dadosSemana as $item) : ?>
                <tr>
                  <td style="text-align: center;"><?= date('d/m/Y', strtotime($item['DataProducao'])) ?></td>
                  <td><?= mb_strimwidth($item['Caderno'], 0, 20, '...') ?></td>
                  <td title="<?= htmlspecialchars($item['Gramatura']) ?>"><?= htmlspecialchars(mb_strimwidth($item['Gramatura'], 0, 11, '...')) ?></td>
                  <td style="text-align: center;"><?= $item['QtdeChapa'] ?></td>
                  <td style="text-align: center;"><?= $item['TrocaBobina'] ?></td>
                  <td style="text-align: center;"><?= $item['QuebraPapel'] ?></td>
                  <td style="text-align: center;"><?= $item['DefeitoChapa'] ?></td>
                  <td style="text-align: right;"><?= $item['Maquina'] ?></td>
                  <td style="text-align: right;"><?= $item['TiragemLiq'] !== '' ? number_format($item['TiragemLiq'], 0, ',', '.') : '' ?></td>
                  <td style="text-align: right;"><?= $item['TiragemBru'] !== '' ? number_format($item['TiragemBru'], 0, ',', '.') : '' ?></td>
                  <td style="text-align: right;"><?= $item['TiragemDif'] !== '' ? number_format($item['TiragemDif'], 0, ',', '.') : '' ?></td>
                  <td style="text-align: center;"><?= date('H:i', strtotime($item['HoraInicio'])) ?></td>
                  <td style="text-align: center;"><?= date('H:i', strtotime($item['HoraFim'])) ?></td>
                  <td style="text-align: center;"><?= $item['Duracao'] ?></td>
                  <td style="text-align: right;"><?= $item['Kilo'] !== '' ? number_format($item['Kilo'], 6, ',', '.') : '' ?></td>
                  <td style="text-align: right;"><?= $item['NumeroOP'] ?></td>
                  <td title="<?= $item['Obs'] ?>"><?= mb_strimwidth($item['Obs'], 0, 20, '...') ?></td>
                  <td><button class="btn btn-primary btn-sm" onclick="openEditarModal(<?= htmlspecialchars(json_encode($item)) ?>)">Editar</button></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tbody>
              <tr>
                <th colspan="9" style="text-align: right;">Total Geral:</th>
                <th style="text-align: right;">
                  <?php
                  $TotalTiragemLiq = array_sum(array_column($dadosSemana, 'TiragemLiq'));
                  echo number_format($TotalTiragemLiq, 0, ',', '.');
                  ?>
                </th>
                <th style="text-align: right;">
                  <?php
                  $TotalTiragemBru = array_sum(array_column($dadosSemana, 'TiragemBru'));
                  echo number_format($TotalTiragemBru, 0, ',', '.');
                  ?>
                </th>
                <th style="text-align: right;">
                  <?php
                  $TotalTiragemDif = array_sum(array_column($dadosSemana, 'TiragemDif'));
                  echo number_format($TotalTiragemDif, 0, ',', '.');
                  ?>
                </th>
                <th colspan="3" style="text-align: right;">
                  <?php
                  $TotalHoras = array_reduce($dadosSemana, function ($carry, $item) {
                    $parts = explode(':', $item['Duracao']);
                    $carry += $parts[0] * 60 + $parts[1];
                    return $carry;
                  }, 0);
                  $hours = floor($TotalHoras / 60);
                  $minutes = $TotalHoras % 60;
                  echo sprintf('%02d:%02d', $hours, $minutes);
                  ?>
                </th>
                <th style="text-align: right;">
                  <?php
                  $Totalkilo = array_sum(array_column($dadosSemana, 'Kilo'));
                  echo number_format($Totalkilo, 0, ',', '.');
                  ?>
                <th colspan="3"></th>
              </tr>
            </tbody>
          </table>
          <div class="mb-3"></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Inclui Modal -->
<?php require_once __DIR__ . '/../includes/modals/graf_producao.php'; ?>

<!-- Inclui JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/graf_producao.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>