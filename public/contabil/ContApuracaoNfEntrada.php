<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/ContApuracaoNfEntrada.php';

$Titulo = 'Contabilização - Nota de Entrada';
$URL = URL_PRINCIPAL . 'contabil/ContApuracaoNfEntrada.php';

// Instanciar a classe
$ContabilApuracaoNfEntrada = new ContabilApuracaoNfEntrada();

if (isset($_POST['btn-buscar'])) {
  $mesAno = $_POST['MesAno'] ?? date('m/Y');
  $Consultas = $ContabilApuracaoNfEntrada->gerarRelatorio($mesAno);

  $ConsultaNotasEntrada = $Consultas['notasEntrada'];
  $TotalNF = count($ConsultaNotasEntrada);

  $ConsultaLoteContabil = $Consultas['loteContabil'];
  $TotalLote = count($ConsultaLoteContabil);
}

// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center filter-fields">
  <div class="col col-sm-4">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="form" name="form">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Mes / Ano</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <select class="form-select form-select-sm" id="MesAno" name="MesAno">
                <option value="0">-- Selecione --</option>
                <?php
                $currentMonth = date('m');
                $currentYear = date('Y');

                // Loop pelos 12 meses anteriores
                for ($i = 0; $i < 12; $i++) {
                  $mesAno = date('m/Y', strtotime("-$i month", strtotime("$currentYear-$currentMonth-01")));
                  $selected = ($mesAno == $MesAnoSelecionado) ? 'selected' : '';
                  echo "<option value=\"$mesAno\" $selected>$mesAno</option>";
                }
                ?>
              </select>
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            <!-- <button id="btn-exportar" name="btn-exportar" type="submit" class="btn btn-success btn-sm">Exportar</button> -->
            <a class="btn btn-primary btn-sm" href="<?= URL_PRINCIPAL ?>">Voltar</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<!-- Resultado -->
<?php if (isset($Consultas)) : ?>
  <?php
  $agrupamentonf = [];
  foreach ($ConsultaNotasEntrada as $item) {
    $agrupamentonf[$item['NFC_DATENT']][] = $item;
  }
  ?>
  <div class="container">
    <!-- Exibindo Nota Entrada -->
    <div class="row gy-4">
      <div class="col-md-6">
        <div class="card shadow-sm h-100">
          <h5 class="card-header bg-primary text-white">Consulta Nota Entrada</h5>
          <div class="card-body">
            <table id="Comunicacao" class="table table-striped table-hover mb-0" style="border: 1px solid #ccc;">
              <thead>
                <tr class="table-primary">
                  <th>Data Entrada</th>
                  <th>Vlr. BPR</th>
                  <th>Vlr. BSE</th>
                  <th>Vlr. Total</th>
                  <th>Vlr. Dif. </th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($agrupamentonf as $date => $itens) : ?>
                  <?php
                  $totalBPR = array_sum(array_column($itens, 'VLRBPR'));
                  $totalBSE = array_sum(array_column($itens, 'VLRBSE'));
                  $valorTotal = $totalBPR + $totalBSE;

                  // Calculando o valor correspondente em ConsultaLoteContabil
                  $totInf = 0;
                  foreach ($ConsultaLoteContabil as $lote) {
                    if ($date == $lote['DATLOT']) {
                      $totInf = $lote['TOTINF']; // Para o exemplo, só pegando o último valor. Você deve ajustar conforme sua lógica.
                      break;
                    }
                  }
                  // Calculando a diferença
                  $diferenca = $valorTotal - $totInf;
                  $status = ($diferenca >= 0 && $diferenca <= 1) ? 'OK' : 'X';
                  ?>
                  <tr>
                    <td><?= date('d/m/Y', strtotime($date)) ?></td>
                    <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($totalBPR, 2, ',', '.') ?></td>
                    <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($totalBSE, 2, ',', '.') ?></td>
                    <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($valorTotal, 2, ',', '.') ?></td>
                    <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($diferenca, 2, ',', '.') ?></td>
                    <td class="align-center" style="font-weight: bold; color: <?= $status == 'OK' ? 'blue' : 'red' ?>; text-align: center;"><?= $status ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tbody>
                <tr class="table-primary">
                  <th>Total Geral:</th>
                  <th style="text-align: right;"><span style="float: left;">R$ </span><?= number_format(array_sum(array_column($ConsultaNotasEntrada, 'VLRBPR')), 2, ',', '.') ?></th>
                  <th style="text-align: right;"><span style="float: left;">R$ </span><?= number_format(array_sum(array_column($ConsultaNotasEntrada, 'VLRBSE')), 2, ',', '.') ?></th>
                  <th style="text-align: right;"><span style="float: left;">R$ </span><?= number_format(array_sum(array_column($ConsultaNotasEntrada, 'VLRBPR')) + array_sum(array_column($ConsultaNotasEntrada, 'VLRBSE')), 2, ',', '.') ?></th>
                  <th style="text-align: right;"><span style="float: left;">R$ </span>
                    <?= number_format((
                      array_sum(array_column($ConsultaNotasEntrada, 'VLRBPR')) +
                      array_sum(array_column($ConsultaNotasEntrada, 'VLRBSE'))) -
                      array_sum(array_column($ConsultaLoteContabil, 'TOTINF')), 2, ',', '.') ?>
                  </th>
                  <th></th>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Exibindo Lote Contabil -->
      <div class="col-md-6">
        <div class="card shadow-sm h-100">
          <h5 class="card-header bg-primary text-white">Consulta Lote Contabil</h5>
          <div class="card-body">
            <table id="Comunicacao" class="table table-striped table-hover" style="border: 1px solid #ccc;">
              <thead>
                <tr class="table-primary">
                  <th>Data Lote</th>
                  <th>Vlr. Total Deb.</th>
                  <th>Vlr. Total Cre.</th>
                  <th>Vlr. Total</th>
                  <th>Vlr. Dif. </th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($ConsultaLoteContabil as $item): ?>
                  <?php
                  $totDeb = $item['TOTDEB'];
                  $totCre = $item['TOTCRE'];
                  $valorTotalLote = $item['TOTINF'];

                  // Inicializa o valor total das notas de entrada como 0
                  $valorTotalNotas = 0;

                  // Procura a data correspondente em ConsultaNotasEntrada
                  foreach ($ConsultaNotasEntrada as $nota) {
                    if ($item['DATLOT'] == $nota['NFC_DATENT']) {
                      $valorTotalNotas += $nota['VLRBPR'] + $nota['VLRBSE'];
                    }
                  }

                  // Calcule a diferença
                  $diferencal = $valorTotalLote - $valorTotalNotas; // Lote - Notas de Entrada
                  $statusl = ($diferencal >= 0 && $diferencal <= 1) ? 'OK' : 'X';
                  ?>
                  <tr>
                    <td><?= date('d/m/Y', strtotime($item['DATLOT'])) ?></td>
                    <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($item['TOTDEB'], 2, ',', '.') ?></td>
                    <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($item['TOTCRE'], 2, ',', '.') ?></td>
                    <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($item['TOTINF'], 2, ',', '.') ?></td>
                    <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($diferencal, 2, ',', '.') ?></td>
                    <td class="align-center" style="font-weight: bold; color: <?= $statusl == 'OK' ? 'blue' : 'red' ?>; text-align: center;"><?= $statusl ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tbody>
                <tr class="table-primary">
                  <th>Total Geral:</th>
                  <th style="text-align: right;"><span style="float: left;">R$ </span><?= number_format(array_sum(array_column($ConsultaLoteContabil, 'TOTDEB')), 2, ',', '.') ?></th>
                  <th style="text-align: right;"><span style="float: left;">R$ </span><?= number_format(array_sum(array_column($ConsultaLoteContabil, 'TOTINF')), 2, ',', '.') ?></th>
                  <th style="text-align: right;"><span style="float: left;">R$ </span><?= number_format(array_sum(array_column($ConsultaLoteContabil, 'TOTINF')), 2, ',', '.') ?></th>
                  <th style="text-align: right;"><span style="float: left;">R$ </span>
                    <?= number_format(
                      array_sum(array_column($ConsultaLoteContabil, 'TOTINF')) -
                        (array_sum(array_column($ConsultaNotasEntrada, 'VLRBPR')) +
                          array_sum(array_column($ConsultaNotasEntrada, 'VLRBSE'))),
                      2,
                      ',',
                      '.'
                    )
                    ?>
                  </th>
                  <th colspan="2"></th>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Espaço entre os resultados -->
    <div class="mb-3"></div>

    <!-- Exibindo Notas Entrada Por dia -->
    <?php
    $agrupamentonf = [];
    foreach ($ConsultaNotasEntrada as $item) {
      $agrupamentonf[$item['NFC_DATENT']][] = $item;
    }
    ?>
    <?php foreach ($agrupamentonf as $key): ?>
      <?php
      // Verifica se há algum item com NUMLOT diferente de '0'
      $NotaSemLote = false;
      foreach ($key as $nota) {
        if ($nota['NUMLOT'] == '0') {
          $NotaSemLote = true;
          break; // Se encontrarmos uma nota, podemos parar a iteração
        }
      }
      ?>
      <?php if ($NotaSemLote): ?>
        <div class="card shadow-sm h-100">
          <h5 class="card-header bg-primary text-white">
            Notas Entrada - <?= date('d/m/Y', strtotime($key[0]['NFC_DATENT'])) ?>
          </h5>
          <div class="card-body">
            <table id="Comunicacao" class="table table-striped table-hover" style="border: 1px solid #ccc;">
              <thead>
                <tr class="table-primary">
                  <th>Nº. Nota</th>
                  <th>Série</th>
                  <th>Cod. For.</th>
                  <th>Dat. Entrada</th>
                  <th>TNS Pro.</th>
                  <th>TNS Ser.</th>
                  <th>Vlr. Base Pro.</th>
                  <th>Vlr. Base Ser.</th>
                  <th>Nº Lote</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($key as $nota): ?>
                  <?php if ($nota['NUMLOT'] == '0') : ?>
                    <tr>
                      <td><?= $nota['NFC_NUMNFC'] ?></td>
                      <td><?= $nota['CODSNF'] ?></td>
                      <td><?= $nota['NFC_CODFOR'] ?></td>
                      <td><?= date('d/m/Y', strtotime($nota['NFC_DATENT'])) ?></td>
                      <td><?= $nota['TNSPRO'] ?></td>
                      <td><?= $nota['TNSSER'] ?></td>
                      <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($nota['VLRBPR'], 2, ',', '.') ?></td>
                      <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($nota['VLRBSE'], 2, ',', '.') ?></td>
                      <td><?= $nota['NUMLOT'] ?></td>
                      <td style="text-align: center; font-weight: bold; color: <?= $nota['NUMLOT'] > '0' ? 'blue' : 'red' ?>;">
                        <?= $nota['NUMLOT'] > '0' ? 'Sim' : 'Não' ?>
                      </td>
                    </tr>
                  <?php endif; ?>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endif; ?>
      <!-- Espaço entre os resultados -->
      <div class="mb-2"></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- JavaScript -->
<script src="../js/cont_recoperacionais.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>