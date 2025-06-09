<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/ContConfereLoteOri.php';

$Titulo = 'Conferencia Lotes Contábeis - Origem';
$URL = URL_PRINCIPAL . 'contabil/ContConfereLoteOri.php';

// Instanciar a classe
$ConsultaLoteContabil = new LoteContabil();

// Busca Origem para filtro
$ConsultaOrigem = $ConsultaLoteContabil->consultaOrigem();

if (isset($_POST['btn-buscar'])) {
  $codEmpresa = $_POST['CodEmp'];
  $mesAno     = $_POST['MesAno'];
  $origem     = $_POST['Origem'];

  // echo "<pre>";
  // var_dump($codEmpresa, $mesAno, $origem);
  // die();

  $ConsultaLote = $ConsultaLoteContabil->consultaLoteContabil($codEmpresa, $mesAno, $origem);
  $Total = COUNT($ConsultaLote);


  if ($Total > 0) {
    //  Extrai Numero de Lotes
    $numLotValues = array_column($ConsultaLote, 'NUMLOT');

    // echo "<pre>";
    // var_dump($numLotValues);
    // die();
    $ConsultaLancamento = $ConsultaLoteContabil->consultaLancamentoLoteContabil($numLotValues);
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
              <strong>Cód. Empresa:</strong>
            </div>
            <div class="col">
              <strong>Mes/Ano</strong>
            </div>
            <div class="col">
              <strong>Origem</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <select class="form-select form-select-sm" id="CodEmp" name="CodEmp" placeholder="Conta Reduzida">
                <option value="0">-- Selecione Empresa --</option>
                <option value="1">1 - Diário da Região</option>
                <option value="2">2 - FM Diário</option>
              </select>
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="MesAno" name="MesAno" placeholder="MM/YYYY">
            </div>
            <div class="col">
              <select class="form-select form-select-sm" id="Origem" name="Origem">
                <option value="Todos" disabled selected>-- Selecione --</option>
                <?php foreach ($ConsultaOrigem as $origem): ?>
                  <option value="<?= htmlspecialchars($origem['ORILCT']) ?>">
                    <?= htmlspecialchars($origem['ORILCT']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            <a class="btn btn-primary btn-sm" href="<?= URL_PRINCIPAL ?>">Voltar</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<!-- Exibindo Resultado -->
<?php if (!empty($Total)): ?>
  <div class="container">
    <div class="card shadow-sm ">
      <h5 class="card-header bg-primary text-white">Consulta Nota Saída</h5>
      <div class="card-body table-responsive">
        <table id="NotaSaida" class="table table-striped full-width-table mb-0">
          <thead>
            <tr class="table-primary">
              <th>N.º Lote</th>
              <th>Situação</th>
              <th>Data</th>
              <th>Vlr. Créditos</th>
              <th>Vlr. Débitos</th>
              <th>Vlr. Diferença</th>
              <th>Vlr. Total</th>
              <th>Origem</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ConsultaLote as $key => $item) : ?>
              <tr>
                <td><?= $item['NUMLOT'] ?></td>
                <th><?= $item['SITLOT'] ?></th>
                <td><?= date('d/m/Y', strtotime($item['DATLOT'])) ?></td>
                <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($item['TOTCRELCT'], 2, ',', '.') ?></td>
                <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($item['TOTDEBLCT'], 2, ',', '.') ?></td>
                <td style="text-align: right; font-weight: <?= (($item['TOTCRELCT'] - $item['TOTDEBLCT'] > 0) || $item['TOTCRELCT'] - $item['TOTDEBLCT'] < 0) ? 'bold' : 'normal' ?>; color: <?= (($item['TOTCRELCT'] - $item['TOTDEBLCT'] > 0) || $item['TOTCRELCT'] - $item['TOTDEBLCT'] < 0) ? 'red' : 'black' ?>;">
                  <span style="float: left;">R$ </span><?= number_format($item['TOTCRELCT'] - $item['TOTDEBLCT'], 2, ',', '.') ?>
                </td>
                <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($item['TOTINF'], 2, ',', '.') ?></td>
                <td><?= $item['ORILCT'] ?></td>
                <td class="align-center" style="font-weight: bold; color: <?= (($item['TOTCRELCT'] - $item['TOTDEBLCT'] > 0) || $item['TOTCRELCT'] - $item['TOTDEBLCT'] < 0) ? 'red' : 'blue' ?>; text-align: center;"><?= (($item['TOTCRELCT'] - $item['TOTDEBLCT'] > 0) || $item['TOTCRELCT'] - $item['TOTDEBLCT'] < 0) ? 'X' : 'OK' ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tbody>
            <tr class="table-primary">
              <th colspan=3 style="text-align: right;">Total Geral:</th>
              <th style="text-align: right;"><span style="float: left;">R$ </span><?= number_format(array_sum(array_column($ConsultaLote, 'TOTCRELCT')), 2, ',', '.') ?></th>
              <th style="text-align: right;"><span style="float: left;">R$ </span><?= number_format(array_sum(array_column($ConsultaLote, 'TOTDEBLCT')), 2, ',', '.') ?></th>
              <th style="text-align: right;"><span style="float: left;">R$ </span><?= number_format(array_sum(array_column($ConsultaLote, 'TOTCRELCT')) - array_sum(array_column($ConsultaLote, 'TOTDEBLCT')), 2, ',', '.') ?></th>
              <th style="text-align: right;"><span style="float: left;">R$ </span><?= number_format(array_sum(array_column($ConsultaLote, 'TOTINF')), 2, ',', '.') ?></th>
              <th colspan="9" style="text-align: right;"></th>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Espacamento -->
  <div class="mb-3"></div>

  <!-- Exibindo Resultado Lançamentos -->
  <?php if (!empty($ConsultaLancamento)) : ?>
    <?php
    // Agrupa os lançamentos por data
    $lancamentosPorData = [];
    foreach ($ConsultaLancamento as $lancamento) {
      $data = $lancamento['DATLCT'];
      $lancamentosPorData[$data][] = $lancamento;
    }

    foreach ($lancamentosPorData as $data => $lancamentos) {
      // Calcula os totais para cada data
      $totalDebito  = 0;
      $totalCredito = 0;
      foreach ($lancamentos as $item) {
        if ($item['CTADEB'] > 0) {
          $totalDebito += $item['VLRLCT'];
        }
        if ($item['CTACRE'] > 0) {
          $totalCredito += $item['VLRLCT'];
        }
      }
      // Formata os totais
      $diferenca    = number_format($totalCredito - $totalDebito, 2, ',', '.');
      $totalDebito  = number_format($totalDebito, 2, ',', '.');
      $totalCredito = number_format($totalCredito, 2, ',', '.');
    }
    ?>
    <?php if ($diferenca != 0): ?>
      <div class="container">
        <div class="card shadow-sm ">
          <h5 class="card-header bg-primary text-white">Consulta Nota Saída</h5>
          <div class="card-body table-responsive">
            <table id="Lancamentos" class="table table-striped full-width-table">
              <thead>
                <tr class="table-primary toggle-summary gray-background" style="cursor: pointer;">
                  <th colspan="2" style="text-align: left;">Total Lançamento - <?= date('d/m/Y', strtotime($data)) ?></th>
                  <th style="text-align: right;"><span style="float: left;">Total Débito - R$ </span><?= $totalDebito ?></th>
                  <th style="text-align: right;"><span style="float: left;">Total Crédito - R$ </span><?= $totalCredito ?></th>
                  <th colspan="2" style="text-align: center;"><span style="float: left;">Diferença - R$ </span><?= $diferenca ?></th>
                  <th colspan="2" style="text-align: center;">Clique para ver detalhes</th>
                </tr>
              </thead>
              <!-- Corpo com os detalhes; inicialmente oculto -->
              <tbody class="toggle-details" style="display: none;">
                <?php
                // Agrupa os lançamentos do dia pelo campo CPLLCT
                $lancamentosPorComplemento = [];
                foreach ($lancamentos as $item) {
                  // Use o valor do campo para agrupar; se estiver vazio, pode definir um rótulo padrão.
                  $complemento = $item['CPLLCT'] ?: 'Sem Complemento';
                  $lancamentosPorComplemento[$complemento][] = $item;
                }

                // Itera sobre cada grupo
                foreach ($lancamentosPorComplemento as $complemento => $itens):
                  // Calcula os totais para cada data
                  $totalDebito1  = 0;
                  $totalCredito1 = 0;
                  foreach ($itens as $item) {
                    if ($item['CTADEB'] > 0) {
                      $totalDebito1 += $item['VLRLCT'];
                    }
                    if ($item['CTACRE'] > 0) {
                      $totalCredito1 += $item['VLRLCT'];
                    }
                  }
                  // Formata os totais
                  $diferenca1    = number_format($totalCredito1 - $totalDebito1, 2, ',', '.');
                  $totalDebito1  = number_format($totalDebito1, 2, ',', '.');
                  $totalCredito1 = number_format($totalCredito1, 2, ',', '.');
                ?>
                  <?php if ($diferenca1 != 0) : ?>
                    <!-- Linha para identificar o grupo por CPLLCT -->
                    <tr style="background: #e9ecef; font-weight: bold;">
                      <td colspan="8">Complemento: <?= $complemento ?></td>
                    </tr>
                    <?php foreach ($itens as $item): ?>
                      <tr>
                        <td><?= $item['NUMLCT'] ?></td>
                        <td><?= date('d/m/Y', strtotime($item['DATLCT'])) ?></td>
                        <td><?= $item['CTADEB'] ?></td>
                        <td><?= $item['CTACRE'] ?></td>
                        <td style="text-align: right;">
                          <span style="float: left;">R$ </span><?= number_format($item['VLRLCT'], 2, ',', '.') ?>
                        </td>
                        <td><?= $item['ORILCT'] ?></td>
                        <td colspan="2"><?= $item['CPLLCT'] ?></td>
                      </tr>
                    <?php endforeach; ?>
                    <!-- Linha de totalização por CPLLCT -->
                    <tr style="background: #f5f5f5; font-weight: bold;">
                      <td colspan="2" style="text-align: right;">Total:</td>
                      <td style="text-align: right;"><span style="float: left;">R$ </span><?= $totalDebito1 ?></td>
                      <td style="text-align: right;"><span style="float: left;">R$ </span><?= $totalCredito1 ?></td>
                      <td colspan="3" style="text-align: center;"><span style="float: left;">Diferença - R$ </span><?= $diferenca1 ?></td>
                    </tr>
                  <?php endif; ?>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>
<?php endif; ?>

<!-- Inclui o JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>
<script src="<?= URL_PRINCIPAL ?>js/cont_conflotecontabilori.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
