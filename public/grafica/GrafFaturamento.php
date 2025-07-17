<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/GrafFaturamento.php';

$Titulo = 'Gráfica - Faturamento';
$URL = URL_PRINCIPAL . 'grafica/GrafFaturamento.php';

// Instanciar a classe
$GraficaFaturamento = new GraficaFaturamento();

if (isset($_POST['btn-buscar'])) {
  $dados = $_POST;

  $consultaFaturamento = $GraficaFaturamento->consultaFaturamento($dados);
  $TotalCon = count($consultaFaturamento);

  $dadosAgrupados = array();
  foreach ($consultaFaturamento as $item) {
    $dataRodagem = $item['DataRodagem'];
    if (!isset($dadosAgrupados[$dataRodagem])) {
      $dadosAgrupados[$dataRodagem] = array();
    }
    $dadosAgrupados[$dataRodagem][] = $item;
  }
} else if (isset($_POST['btn-relatorio'])) {
  $dados = $_POST;

  $relatorioFaturamento = $GraficaFaturamento->relatorioFaturamento($dados);
  $TotalRel = count($relatorioFaturamento);

  $TotalPed = count(array_filter(array_column($relatorioFaturamento, 'numped')));
  $totalTiragem = array_sum(array_column($relatorioFaturamento, 'Tiragem'));
  $totalValor = array_sum(array_column($relatorioFaturamento, 'Valor'));
  $totalVlrOri = array_sum(array_column($relatorioFaturamento, 'vlrori'));
} else if (isset($_POST['BtnIncluirModal'])) {
  $dados = $_POST;
  $incluir = $GraficaFaturamento->incluirFaturamento($dados);

  if (!empty($dados['DataRodagem'])) {
    $dados = [
      'dtInicio' => $dados['DataRodagem'],
      'dtFim'    => '',
      'tipo'     => '',
      'cliente'  => '',
      'arte'     => '',
      'faturado' => ''
    ];
  }

  $consultaFaturamento = $GraficaFaturamento->consultaFaturamento($dados);
  $TotalCon = count($consultaFaturamento);

  $dadosAgrupados = array();
  foreach ($consultaFaturamento as $item) {
    $dataRodagem = $item['DataRodagem'];
    if (!isset($dadosAgrupados[$dataRodagem])) {
      $dadosAgrupados[$dataRodagem] = array();
    }
    $dadosAgrupados[$dataRodagem][] = $item;
  }
} else if (isset($_POST['BtnSalvarModal'])) {
  $dados = $_POST;
  $editar = $GraficaFaturamento->editarFaturamento($dados);

  if (!empty($dados['DataRodagem'])) {
    $dados = [
      'dtInicio' => date('Y-m-d', strtotime(str_replace('/', '-', $dados['DataRodagem']))),
      'dtFim'    => '',
      'tipo'     => '',
      'cliente'  => '',
      'arte'     => '',
      'faturado' => ''
    ];
  }

  $consultaFaturamento = $GraficaFaturamento->consultaFaturamento($dados);
  $TotalCon = count($consultaFaturamento);

  $dadosAgrupados = array();
  foreach ($consultaFaturamento as $item) {
    $dataRodagem = $item['DataRodagem'];
    if (!isset($dadosAgrupados[$dataRodagem])) {
      $dadosAgrupados[$dataRodagem] = array();
    }
    $dadosAgrupados[$dataRodagem][] = $item;
  }
} else if (isset($_POST['btn-duplicar'])) {
  $dados = $_POST;
  $duplicar = $GraficaFaturamento->duplicarFaturamento($dados);

  if (!empty($dados['NovaData'])) {
    $dados = [
      'dtInicio' => $dados['NovaData'],
      'dtFim'    => '',
      'tipo'     => '',
      'cliente'  => '',
      'arte'     => '',
      'faturado' => ''
    ];
  }

  $consultaFaturamento = $GraficaFaturamento->consultaFaturamento($dados);
  $TotalCon = count($consultaFaturamento);

  $dadosAgrupados = array();
  foreach ($consultaFaturamento as $item) {
    $dataRodagem = $item['DataRodagem'];
    if (!isset($dadosAgrupados[$dataRodagem])) {
      $dadosAgrupados[$dataRodagem] = array();
    }
    $dadosAgrupados[$dataRodagem][] = $item;
  }
} else if (isset($_POST['btn-alteraData'])) {
  $dados = $_POST;
  $alteraData = $GraficaFaturamento->alteraDataFaturamento($dados);

  if (!empty($dados['NovaData'])) {
    $dados = [
      'dtInicio' => $dados['NovaData'],
      'dtFim'    => '',
      'tipo'     => '',
      'cliente'  => '',
      'arte'     => '',
      'faturado' => ''
    ];
  }

  $consultaFaturamento = $GraficaFaturamento->consultaFaturamento($dados);
  $TotalCon = count($consultaFaturamento);

  $dadosAgrupados = array();
  foreach ($consultaFaturamento as $item) {
    $dataRodagem = $item['DataRodagem'];
    if (!isset($dadosAgrupados[$dataRodagem])) {
      $dadosAgrupados[$dataRodagem] = array();
    }
    $dadosAgrupados[$dataRodagem][] = $item;
  }
} else if (isset($_POST['btn-faturado'])) {
  $dados = $_POST;

  $faturado = $GraficaFaturamento->alteraFaturamento($dados);
  $TotalCon = count($faturado);

  $dadosAgrupados = array();
  foreach ($faturado as $item) {
    $dataRodagem = $item['DataRodagem'];
    if (!isset($dadosAgrupados[$dataRodagem])) {
      $dadosAgrupados[$dataRodagem] = array();
    }
    $dadosAgrupados[$dataRodagem][] = $item;
  }
} else if (isset($_POST['BtnExcluirModal'])) {
  $dados = $_POST;
  $excluir = $GraficaFaturamento->excluirFaturamento($dados);

  if (!empty($dados['DataRodagem'])) {
    $dados = [
      'dtInicio' => date('Y-m-d', strtotime(str_replace('/', '-', $dados['DataRodagem']))),
      'dtFim'    => '',
      'tipo'     => '',
      'cliente'  => '',
      'arte'     => '',
      'faturado' => ''
    ];
  }

  $consultaFaturamento = $GraficaFaturamento->consultaFaturamento($dados);
  $TotalCon = count($consultaFaturamento);

  $dadosAgrupados = array();
  foreach ($consultaFaturamento as $item) {
    $dataRodagem = $item['DataRodagem'];
    if (!isset($dadosAgrupados[$dataRodagem])) {
      $dadosAgrupados[$dataRodagem] = array();
    }
    $dadosAgrupados[$dataRodagem][] = $item;
  }
}
// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center filter-fields">
  <div class="col col-sm-8">
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
              <strong>Tipo</strong>
            </div>
            <div class="col">
              <strong>Cliente</strong>
            </div>
            <div class="col">
              <strong>Arte</strong>
            </div>
            <div class="col">
              <strong>Faturado</strong>
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
              <select class="form-select form-select-sm" id="tipo" name="tipo">
                <option value="">-- Selecione --</option>
                <option value="Comercial">Comercial</option>
                <option value="Digital">Digital</option>
                <option value="Editorial">Editorial</option>
                <option value="Embalagem">Embalagem</option>
                <option value="Papel">Papel</option>
                <option value="Terceiro">Terceiro</option>
              </select>
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="cliente" name="cliente">
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="arte" name="arte">
            </div>
            <div class="col">
              <select class="form-select form-select-sm" id="faturado" name="faturado">
                <option value="">-- Selecione --</option>
                <option value="1">Sim</option>
                <option value="2">Não</option>
              </select>
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            <button id="btn-relatorio" name="btn-relatorio" type="submit" class="btn btn-primary btn-sm">Relatório</button>
            <button id="btn-incluir" name="btn-incluir" type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#IncluirModal">Incluir</button>
            <button id="btn-exportar" name="btn-exportar" type="submit" class="btn btn-success btn-sm">Exportar</button>
            <button id="btn-imprimir" name="btn-imprimir" type="submit" class="btn btn-primary btn-sm">Imprimir</button>
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
<?php if (!empty($TotalCon)) : ?>
  <div class="container">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <?php foreach ($dadosAgrupados as $semaanaAno => $dadosSemana) : ?>
          <h5 class="card-header bg-primary text-white">
            Qtde. Total: <?= count($dadosSemana) ?>
          </h5>
          <table class="table table-striped table-hover mb-0" id="FaturamentoGrafica" name="FaturamentoGrafica">
            <thead>
              <tr class="table-primary">
                <th scope="col">Dia Semana</th>
                <th scope="col">Data Rodagem</th>
                <th scope="col">Cliente</th>
                <th scope="col">Arte</th>
                <th scope="col">Tipo</th>
                <th scope="col">Formato</th>
                <th scope="col">Papel</th>
                <th scope="col">Qtde. Cor</th>
                <th scope="col">Tiragem</th>
                <th scope="col">Valor</th>
                <th scope="col">Faturado</th>
                <th scope="col">NumPed</th>
                <th scope="col">PedCli</th>
                <th scope="col">Obs.</th>
                <th scope="col" colspan="2">Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($dadosSemana as $item) : ?>
                <tr>
                  <td style="white-space: nowrap;"><?= $item['DiaSemana'] ?></td>
                  <td class="text-center"><?= date('d/m/Y', strtotime($item['DataRodagem'])) ?></td>
                  <td><?= mb_strimwidth($item['Cliente'], 0, 20, '...') ?></td>
                  <td><?= $item['Arte'] ?></td>
                  <td><?= $item['Tipo'] ?></td>
                  <td><?= $item['Formato'] ?></td>
                  <td><?= $item['Papel'] ?></td>
                  <td><?= $item['QtdeCor'] ?></td>
                  <td style="text-align: right;"><?= $item['Tiragem'] !== '' ? number_format($item['Tiragem'], 0, ',', '.') : '' ?></td>
                  <td style="text-align: right; <?= $item['Valor'] < 0 ? 'color: red; font-weight: bold;' : '' ?>; white-space: nowrap;">
                    <span style="float: left;">R$</span>
                    <?= number_format(floatval($item['Valor']), 2, ',', '.'); ?>
                  </td>
                  <td class="text-center"><?= $item['Faturado'] ?></td>
                  <td><?= $item['NumPedido'] ?></td>
                  <td><?= $item['NumPedCli'] ?></td>
                  <td title="<?= $item['Obs'] ?>"><?= mb_strimwidth($item['Obs'], 0, 20, '...') ?></td>
                  <td class="text-center align-middle"><input type="checkbox" name="selected[]" value="<?= $item['ID'] ?>"></td>
                  <td><button class="btn btn-primary btn-sm" onclick="openEditModal(<?= htmlspecialchars(json_encode($item)) ?>)">Editar</button></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tbody>
              <tr>
                <th colspan="9" style="text-align: right;">Valor Total:</th>
                <th style="text-align: right; white-space: nowrap;">
                  <?php $totalValor = array_sum(array_column($dadosSemana, 'Valor')); ?>
                  <span style="float: left; ">R$</span> <?= number_format($totalValor, 2, ',', '.') ?>
                </th>
                <th colspan="7" style="text-align: right;"></th>
              </tr>
            </tbody>
            <tbody>
              <td colspan="16" style="text-align: right;">
                <form action="<?= $URL ?>" id="Altera" name="Altera" method="post" style="display:inline;" onsubmit="return validateForm(event);">
                  <input type="hidden" id="selected_ids" name="selected_ids" required>
                  <button type="submit" id="btn-alteraData" name="btn-alteraData" class="btn btn-primary btn-sm" style="width: auto;" onclick="setSelectedIds()">Alterar Data</button>
                  <button type="submit" id="btn-faturado" name="btn-faturado" class="btn btn-primary btn-sm" style="width: auto;" onclick="setSelectedIds()">Faturado</button>
                  <input type="date" id="NovaData" name="NovaData" class="form-control form-control-sm" style="width: auto; display: inline-block;">
                  <input type="checkbox" id="selectAll" name="selectAll" onclick="toggleSelectAll(this, this.closest('table'))">
                  <button type="submit" id="btn-duplicar" name="btn-duplicar" class="btn btn-primary btn-sm" style="width: auto;" onclick="setSelectedIds()">Duplicar</button>
                </form>
              </td>
            </tbody>
          </table>
          <div class="mb-3"></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Exibindo Relatório -->
<?php if (!empty($TotalRel)) : ?>
  <div class="container">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <h5 class="card-header bg-primary text-white">
          <?php if (!empty($dados['dtInicio']) && !empty($dados['dtFim'])) : ?>
            Relatório de Faturamento de <?= date('d/m/Y', strtotime($dados['dtInicio'])) ?> - <?= date('d/m/Y', strtotime($dados['dtFim'])) ?>
          <?php elseif (!empty($dados['dtInicio']) && empty($dados['dtFim'])) : ?>
            Relatório de Faturamento de <?= date('d/m/Y', strtotime($dados['dtInicio'])) ?>
          <?php endif; ?>
        </h5>
        <div class="row">
          <div class="col-md-7">
            <h6 class="card-header bg-primary text-white">Faturamento</h6>
            <table class="table table-striped table-hover mb-0" id="FaturamentoGrafica" name="FaturamentoGrafica">
              <thead>
                <tr class="table-primary">
                  <th scope="col">Nº.Pedido</th>
                  <th scope="col">Data Rod.</th>
                  <th scope="col">Tipo</th>
                  <th scope="col">Cliente</th>
                  <th scope="col">Tiragem</th>
                  <th scope="col">Valor</th>
                  <th scope="col">Faturado</th>
                  <th scope="col">Obs.</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($relatorioFaturamento as $key => $item) : ?>
                  <tr>
                    <td><?= $item['NumPedCli'] ?></td>
                    <td style="text-align: center;"><?= date('d/m/Y', strtotime($item['DataRodagem'])) ?></td>
                    <td><?= $item['Tipo'] ?></td>
                    <td><?= mb_strimwidth($item['Cliente'], 0, 20, '...') ?></td>
                    <td style="text-align: right;"><?= $item['Tiragem'] !== '' ? number_format($item['Tiragem'], 0, ',', '.') : '' ?></td>
                    <td style="text-align: right; <?= $item['Valor'] < 0 ? 'color: red; font-weight: bold;' : '' ?>; white-space: nowrap;">
                      <span style="float: left;">R$</span>
                      <?= number_format(floatval($item['Valor']), 2, ',', '.'); ?>
                    </td>
                    <td style="text-align: center;"><?= $item['Faturado'] ?></td>
                    <td title="<?= $item['Obs'] ?>"><?= mb_strimwidth($item['Obs'], 0, 20, '...') ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="col-md-5">
            <h6 class="card-header bg-primary text-white">Senior</h6>
            <table class="table table-striped table-hover mb-0" id="FaturamentoGrafica" name="FaturamentoGrafica">
              <thead>
                <tr class="table-primary">
                  <th scope="col">Cod.Cli.</th>
                  <th scope="col">Nº.Nota</th>
                  <th scope="col">Nº.Pedido</th>
                  <th scope="col">Nº.PedCli</th>
                  <th scope="col">Cod.Cpg.</th>
                  <th scope="col">Vlr.Pedido</th>
                  <th scope="col" colspan="2" style="text-align: center;">Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($relatorioFaturamento as $key => $item) : ?>
                  <tr>
                    <td><?= $item['codcli'] ?></td>
                    <td><?= $item['numnfv'] ?></td>
                    <td><?= $item['numped'] ?></td>
                    <td><?= $item['pedcli'] ?></td>
                    <td><?= $item['codcpg'] ?></td>
                    <td style="text-align: right; <?= $item['vlrori'] < 0 ? 'color: red; font-weight: bold;' : '' ?>; white-space: nowrap;">
                      <span style="float: left;">R$</span>
                      <?= number_format(floatval($item['vlrori']), 2, ',', '.'); ?>
                    </td>
                    <td style="font-weight: bold; color: <?= $item['Valor'] == $item['vlrori'] ? 'blue' : 'red' ?>;">
                      <?= $item['Valor'] == $item['vlrori'] ? 'OK' : 'X' ?>
                    </td>
                    <td style="font-weight: bold; color: <?= $item['NumPedCli'] == $item['pedcli'] ? 'blue' : 'red' ?>;">
                      <?= $item['NumPedCli'] == $item['pedcli'] ? 'OK' : 'X' ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="row mb-0">
          <div class="col-md-7">
            <table class="table table-striped table-hover mb-0">
              <tr class="table-primary">
                <th colspan="3" style="text-align: left;">Total Pedidos: <?= $TotalRel ?></th>
                <th style="text-align: right;">Total: <?= number_format($totalTiragem, 0, ',', '.'); ?></th>
                <th style="text-align: right;">R$ <?= number_format($totalValor, 2, ',', '.'); ?></th>
              </tr>
            </table>
          </div>
          <div class="col-md-5">
            <table class="table table-striped table-hover mb-0">
              <tr class="table-primary">
                <th colspan="4" class="align-left">Total Pedidos: <?= $TotalPed ?></th>
                <th class="align-right">R$ <?= number_format($totalVlrOri, 2, ',', '.'); ?></th>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Inclui Modal -->
  <?php require_once __DIR__ . '/../includes/modals/graf_Faturamento.php'; ?>

  <!-- Inclui JavaScript -->
  <script src="<?= URL_PRINCIPAL ?>js/graf_faturamento.js"></script>

  <!-- Inclui o footer da página -->
  <?php
  require_once __DIR__ . '/../includes/footer.php';
  ?>