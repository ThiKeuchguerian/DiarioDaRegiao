<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/ContConfereLote.php';

$Titulo = 'Conferencia Lotes Contábeis';
$URL = URL_PRINCIPAL . 'contabil/ContConfereLote.php';

// Instanciar a classe
$ConsultaLoteContabil = new LoteContabil();

if (isset($_POST['btn-buscar'])) {
  $codEmpresa = $_POST['CodEmp'];
  $mesAno     = $_POST['MesAno'];

  // echo "<pre>";
  // var_dump($codEmpresa, $mesAno);
  // die();

  $ConsultaLote = $ConsultaLoteContabil->consultaLoteContabil($codEmpresa, $mesAno);
  $Total = 0;
  $Total = COUNT($ConsultaLote);

  // echo "<pre>";
  // var_dump($Total);
  // die();

  if ($Total > 0) {
    $ConsultaLancamento = $ConsultaLoteContabil->consultaLancamentoLoteContabil($codEmpresa, $mesAno);
    $TotalLan = 0;
    $TotalLan = COUNT($ConsultaLancamento);

    // Agrupando por Num lote
    $agrupaLote = [];
    foreach ($ConsultaLancamento as $item) {
      $data  = $item['DATLCT'];
      $valor = (float) str_replace(',', '.', $item['VLRLCT']);

      // inicializa o grupo se necessário
      if (!isset($agrupaLote[$data])) {
        $agrupaLote[$data] = [
          'itens'   => [],
          'debito'  => 0.0,
          'credito' => 0.0,
          'dif'     => 0.0,
        ];
      }

      // adiciona ao array de itens
      $agrupaLote[$data]['itens'][] = $item;

      // soma débito ou crédito
      if ($item['CTADEB'] > 0) {
        $agrupaLote[$data]['debito'] += $valor;
      }
      if ($item['CTACRE'] > 0) {
        $agrupaLote[$data]['credito'] += $valor;
      }
    }
    // Calcula a diferença para cada lote
    foreach ($agrupaLote as $data => &$grupo) {
      $dif = $grupo['credito'] - $grupo['debito'];
      $grupo['dif'] = round($dif, 2);
    }
    unset($grupo);
  }
}

// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center">
  <div class="col col-sm-6">
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
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <select class="form-select form-select-sm" id="CodEmp" name="CodEmp" placeholder="Conta Reduzida" required>
                <option value="0">-- Selecione Empresa --</option>
                <option value="1">1 - Diário da Região</option>
                <option value="2">2 - FM Diário</option>
              </select>
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="MesAno" name="MesAno" placeholder="MM/YYYY">
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
    <div class="card shadow-sm">
      <h5 class="card-header bg-primary text-white">Consulta Lote: <?= $mesAno ?></h5>
      <div class="card-body table-responsive">
        <table id="NotaSaida" class="table table-striped full-width-table mb-0">
          <thead>
            <tr class="table-primary">
              <th>Situação</th>
              <th style="text-align: center;">Data</th>
              <th style="text-align: center;">Vlr. Créditos</th>
              <th style="text-align: center;">Vlr. Débitos</th>
              <th style="text-align: center;">Vlr. Diferença</th>
              <th style="text-align: center;">Vlr. Total</th>
              <th style="text-align: center;">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ConsultaLote as $key => $item) : ?>
              <tr>
                <th><?= $item['SITLOT'] ?></th>
                <td style="text-align: center;"><?= date('d/m/Y', strtotime($item['DATLOT'])) ?></td>
                <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($item['TOTCRE'], 2, ',', '.') ?></td>
                <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($item['TOTDEB'], 2, ',', '.') ?></td>
                <td style="text-align: right; font-weight: <?= ($item['DIF'] != 0) ? 'bold' : 'normal' ?>; color: <?= ($item['DIF'] != 0) ? 'red' : 'black' ?>;">
                  <span style="float: left;">R$ </span><?= number_format($item['DIF'], 2, ',', '.') ?>
                </td>
                <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($item['TOTINF'], 2, ',', '.') ?></td>
                <td class="align-center" style="font-weight: bold; color: <?= (($item['TOTCRE'] - $item['TOTDEB'] > 0) || $item['TOTCRE'] - $item['TOTDEB'] < 0) ? 'red' : 'blue' ?>; text-align: center;"><?= (($item['TOTCRE'] - $item['TOTDEB'] > 0) || $item['TOTCRE'] - $item['TOTDEB'] < 0) ? 'XX' : 'OK' ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr class="table-primary">
              <th colspan=2 style="text-align: right;">Total Geral:</th>
              <th style="text-align: right;"><span style="float: left;">R$ </span><?= number_format(array_sum(array_column($ConsultaLote, 'TOTCRE')), 2, ',', '.') ?></th>
              <th style="text-align: right;"><span style="float: left;">R$ </span><?= number_format(array_sum(array_column($ConsultaLote, 'TOTDEB')), 2, ',', '.') ?></th>
              <th style="text-align: right; font-weight: <?= (array_sum(array_column($ConsultaLote, 'DIF')) > 0 || array_sum(array_column($ConsultaLote, 'DIF')) < 0) ? 'bold' : 'normal' ?>; color: <?= (array_sum(array_column($ConsultaLote, 'DIF')) > 0 || array_sum(array_column($ConsultaLote, 'DIF')) < 0) ? 'red' : 'black' ?>;">
                <span style="float: left;">R$ </span><?= number_format(array_sum(array_column($ConsultaLote, 'DIF')), 2, ',', '.') ?>
              </th>
              <th style="text-align: right;"><span style="float: left;">R$ </span><?= number_format(array_sum(array_column($ConsultaLote, 'TOTINF')), 2, ',', '.') ?></th>
              <th colspan="9" style="text-align: right;"></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Espacamento -->
<div class="mb-3"></div>

<!-- Exibindo Resultado Lançamentos -->
<?php if (!empty($TotalLan)) : ?>
  <div class="container">
    <?php foreach ($agrupaLote as $data => $dados): ?>
      <?php if ($dados['dif'] != 0):
        // Agrupar itens por NUMFTC
        $grupos = [];
        foreach ($dados['itens'] as $item) {
          $chave = $item['NUMFTC'] ?: $item['CPLLCT'];
          $grupos[$chave][] = $item;
        }
      ?>
        <div class="card shadow-sm ">
          <h6 class="card-header bg-primary text-white toggle-header">
            Lançamento - <?= date('d/m/Y', strtotime($data)) ?> ||
            Total Débito - R$ <?= number_format($dados['debito'], 2, ',', '.') ?> ||
            Total Crédito - R$ <?= number_format($dados['credito'], 2, ',', '.') ?> ||
            Diferença - R$ <?= number_format($dados['dif'], 2, ',', '.') ?>
          </h6>
          <div class="card-body table-responsive" >
            <table class="table table-striped full-width-table mb-0" style="cursor: pointer;">
              <thead>
                <tr class="table-primary gray-background">
                  <th>Nº. Lanc.</th>
                  <th>Dt. Lanc.</th>
                  <th>Conta Debi.</th>
                  <th>Conta Cred.</th>
                  <th>Vlr. Lanc.</th>
                  <th>Origem</th>
                  <th>Nº. Lote</th>
                  <th>Nº.</th>
                  <th>Complemento</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody class="toggle-details">
                <?php foreach ($grupos as $numFtc => $itensGrupo):
                  $totalDebito  = 0;
                  $totalCredito = 0;
                  foreach ($itensGrupo as $item) {
                    if ($item['CTADEB'] > 0) {
                      $totalDebito += (float) str_replace(',', '.', $item['VLRLCT']);
                    }
                    if ($item['CTACRE'] > 0) {
                      $totalCredito += (float) str_replace(',', '.', $item['VLRLCT']);
                    }
                  }
                  $diferenca = round($totalCredito - $totalDebito, 2);
                  $status = ($diferenca == 0) ? 'OK' : 'XX';
                  if ($diferenca != 0):
                ?>
                  <?php foreach ($itensGrupo as $item): ?>
                    <tr>
                      <td><?= $item['NUMLCT'] ?></td>
                      <td><?= date('d/m/Y', strtotime($item['DATLCT'])) ?></td>
                      <td><?= $item['CTADEB'] ?></td>
                      <td><?= $item['CTACRE'] ?></td>
                      <td style="text-align:right"><span style="float: left;">R$</span><?= number_format((float) str_replace(',', '.', $item['VLRLCT']), 2, ',', '.') ?></td>
                      <td><?= $item['ORILCT'] ?></td>
                      <td><?= $item['NUMLOT'] ?></td>
                      <td><?= $item['NUMFTC'] ?></td>
                      <td><?= $item['CPLLCT'] ?></td>
                      <td style="font-weight: bold; color: <?= ($status === 'OK') ? 'blue' : 'red' ?>; text-align: center;"><?= $status ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php
                  endif;
                endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="mb-2"></div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- Inclui o JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>
<script src="<?= URL_PRINCIPAL ?>js/cont_conflotecontabil.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
