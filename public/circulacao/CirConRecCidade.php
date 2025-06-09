<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/CirConRecCidade.php';

$Titulo = 'Contratos Recebidos por Cidade';
$URL = URL_PRINCIPAL . 'circulacao/CirConRecCidade.php';

// Instanciar a classe
$CirContratosRecebidos = new CirContratosRecebidos();

if (isset($_POST['btn-buscar'])) {
  $ano = $_POST['Ano'];
  $mesAno = $_POST['MesAno'];

  // echo "<pre>";
  // var_dump($ano, $mesAno);
  // die();

  $ConsultaContratos = $CirContratosRecebidos->ConsultaAno($ano, $mesAno);
  $ConsultaAno = COUNT($ConsultaContratos);

  // Agrupa por cidade e soma por mês
  $agrupado = [];
  foreach ($ConsultaContratos as $row) {
    $cidade = $row['Cidade'];
    $mes    = (int) $row['MesPagto'];
    $valor  = (float) str_replace(',', '.', $row['ValorPagoParc']);

    // se ainda não existe a cidade, inicializa 12 meses com zero
    if (!isset($agrupado[$cidade])) {
      $agrupado[$cidade] = array_fill_keys(range(1, 12), 0.0);
    }

    // Só soma se for mes valido
    if ($mes >= 1 && $mes <= 12) {
      $agrupado[$cidade][$mes] += $valor;
    }
  }

  $totaisMes = array_fill_keys(range(1, 12), 0.0);
  foreach ($agrupado as $cidade => $meses) {
    foreach ($meses as $m => $v) {
      $totaisMes[$m] += $v;
    }
  }

  // soma o ano inteiro
  $totalAno = array_sum($totaisMes);
  ksort($agrupado, SORT_NATURAL | SORT_FLAG_CASE);
  // echo "<pre>";
  // var_dump($ConsultaContratos);
  // var_dump($agrupado);
  // die();

} elseif (isset($_POST['btn-analitico'])) {
  $ano = $_POST['Ano'];
  $mesAno = $_POST['MesAno'];

  // echo "<pre>";
  // var_dump($ano, $mesAno);
  // die();

  $ConsultaContratos = $CirContratosRecebidos->ConsultaAno($ano, $mesAno);
  $Analitico = COUNT($ConsultaContratos);

  // Agrupando por Estado
  $agrupado = [];
  foreach ($ConsultaContratos as $row) {
    $UF = $row['UF'];
    if (!isset($agrupado[$UF])) {
      $agrupado[$UF] = [];
    }
    // adiciona a linha dentro do grupo
    $agrupado[$UF][] = $row;
  }
}

// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center filter-fields">
  <div class="col col-sm-6">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="CheckMetas" name="CheckMetas">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Ano</strong>
            </div>
            <div class="col">
              <strong>Mes/Ano</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <select class="form-select form-select-sm" id="Ano" name="Ano">
                <option value="0">-- Ano --</option>
                <?php
                $AnoAtual = date('Y');
                for ($ANO = $AnoAtual; $ANO >= $AnoAtual - 10; $ANO--) {
                  $selected = ($ANO == $AnoSelecionado) ? 'selected' : '';
                  echo "<option value=\"$ANO\" $selected>$ANO</option>";
                }
                ?>
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
            <button id="btn-analitico" name="btn-analitico" type="submit" class="btn btn-primary btn-sm">Analítico</button>
            <a class="btn btn-primary btn-sm" href="<?= URL_PRINCIPAL ?>">Voltar</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<!-- Resultado da Consulta Anual -->
<?php if (isset($ConsultaAno)) : ?>
  <div class="container">
    <div class="card shadow-sm">
      <h5 class="card-header bg-primary text-white">
        Ano: <?= $ano ?> ||
        Total Recebimento: R$ <?= number_format($totalAno, 2, ',', '.') ?>
      </h5>
      <div class="card-body">
        <table class="table table-striped table-hover mb-0">
          <thead>
            <tr class="table-primary">
              <th style="white-space: nowrap;">Cidade</th>
              <?php for ($i = 1; $i <= 12; $i++) : ?>
                <th style="text-align:center"><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>/<?= $ano ?></th>
              <?php endfor; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($agrupado as $cidade => $meses): ?>
              <tr>
                <th><?= htmlspecialchars($cidade) ?></th>
                <?php for ($i = 1; $i <= 12; $i++): ?>
                  <td class="text-end">
                    <span class="float-start">R$ </span>
                    <?= number_format($meses[$i], 2, ',', '.') ?>
                  </td>
                <?php endfor; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr class="table-primary">
              <th>Total Geral</th>
              <?php for ($i = 1; $i <= 12; $i++): ?>
                <th style="text-align: right; white-space: nowrap;">
                  <span style="float: left;">R$</span>
                  <?= number_format($totaisMes[$i], 2, ',', '.') ?>
                </th>
              <?php endfor; ?>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
    <div class="mb-3"></div>
  </div>
<?php endif; ?>

<!-- Resultado da Consulta Analitica -->
<?php if (isset($Analitico)) : ?>
  <div class="container">
    <div class="card shadow-sm">
      <?php foreach ($agrupado as $UF => $itens): ?>
        <?php
        $contratos = array_column($itens, 'Contrato');
        $qtde = count(array_unique($contratos));

        $soma = 0.0;
        foreach ($itens as $key) {
          $soma += (float) str_replace(',', '.', $key['ValorPagoParc']);
        }
        ?>
        <h5 class="card-header bg-primary text-white">
          Mes Ano: <?= $mesAno ?> ||
          UF: <?= $UF ?> ||
          Qtde. Contratos: <?= $qtde ?> ||
          Total Recebimento: R$ <?= number_format($soma, 2, ',', '.') ?>
        </h5>
        <div class="card-body">
          <table class="table table-striped table-hover mb-0">
            <thead>
              <tr class="table-primary">
                <th>Nº.: Contrato / Status</th>
                <th>Nome Cliente</th>
                <th>Cidade</th>
                <th>MesAno Ass.</th>
                <th>Valor Con.</th>
                <th>Nº.: Parc.</th>
                <th>MesAno Ven.</th>
                <th>MesAno Pag.</th>
                <th>Vlr. Parc.</th>
                <th>Vlr. Pago</th>
                <th>Motivo Baixa</th>
                <th>Dif.</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($itens as $item): ?>
                <tr>
                  <td><?= $item['Contrato'] ?> / <?= $item['ContratoAtivo'] ?></td>
                  <td><?= mb_strimwidth(trim($item['NomeRazaoSocial']), 0, 25, '...') ?></td>
                  <td><?= mb_strimwidth(trim($item['Cidade']), 0, 25, '...') ?></td>
                  <td><?= $item['MesAnoAss'] ?></td>
                  <td style="white-space: nowrap; text-align: right;"><span style="float: left;">R$</span><?= number_format($item['ValorContrato'], 2, ',', '.') ?></td>
                  <td style="text-align: center;"><?= $item['Parcela'] ?></td>
                  <td style="text-align: center;"><?= $item['MesAnoVenc'] ?></td>
                  <td style="text-align: center;"><?= $item['MesAnoPagto'] ?></td>
                  <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format($item['ValorParcela'], 2, ',', '.') ?></td>
                  <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format($item['ValorPagoParc'], 2, ',', '.') ?></td>
                  <td><?= trim($item['MotivoBaixaTitulo']) ?></td>
                  <?php
                  if ((float)$item['ValorParcela'] === (float)$item['ValorPagoParc']) {
                    echo '<td><span class="badge bg-primary">OK</span></td>';
                  } else {
                    echo '<td><span class="badge bg-danger">XX</span></td>';
                  }
                  ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endforeach; ?>
      <div class="mb-3"></div>
    </div>
  </div>
<?php endif; ?>

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<!-- Inclui o JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>
<script src="<?= URL_PRINCIPAL ?>js/cir_conreccidade.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
