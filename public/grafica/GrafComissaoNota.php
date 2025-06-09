<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/GrafComissaoNota.php';

$Titulo = 'Comissões/Nota - Vendas Serviços Gráficos';
$URL = URL_PRINCIPAL . 'grafica/GrafComissaoNota.php';

// Instanciar a classe
$GraficaComissaoNota = new GraficaComissaoNota();

$consultaVendedor = $GraficaComissaoNota->consultaVendedor();

if (isset($_POST['btn-buscar'])) {
  $dtInicio = $_POST['DtInicial'];
  $dtFim = $_POST['DtFinal'];
  $codVen = $_POST['codVen'];
  // echo "<pre>";
  // var_dump($dtInicio, $dtFim, $codVen);
  // die();

  $consultaAnalitico = $GraficaComissaoNota->consultaComissao($dtInicio, $dtFim, $codVen);
  $Analitico = COUNT($consultaAnalitico);

  // echo "<pre>";
  // var_dump($Analitico);
  // var_dump($consultaAnalitico[0]);
  // die();

  // Monta o agrupamento em memória
  $dadosAgrupados = [];

  foreach ($consultaAnalitico as $item) {
    $vendedor = $item['Vendedor'];

    // inicializa o grupo se não existir
    if (!isset($dadosAgrupados[$vendedor])) {
      $dadosAgrupados[$vendedor] = [
        'Itens'                  => [],
        'TotalVendasPorVendedor' => 0,
        'SomaComissao'           => 0.0,
        'SomaNota'               => 0.0
      ];
    }

    // adiciona o item à lista de itens
    $dadosAgrupados[$vendedor]['Itens'][] = $item;

    // incrementa o contador de vendas
    $dadosAgrupados[$vendedor]['TotalVendasPorVendedor']++;

    // soma a comissão (converte vírgula para ponto antes)
    $dadosAgrupados[$vendedor]['SomaComissao'] += $item['VlrComis'];
    $dadosAgrupados[$vendedor]['SomaNota']     += $item['VlrNF'];
    ksort($dadosAgrupados, SORT_NATURAL | SORT_FLAG_CASE);
    $totalComissaoGeral = array_sum(array_column($dadosAgrupados, 'SomaComissao'));
  }
}
// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center filter-fields">
  <div class="col col-sm-6">
    <div class="card shadow-sm menu-filtro">
      <form action=<?= $URL ?> method="post" id="form" name="form">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>Data Inicio</strong>
            </div>
            <div class="col">
              <strong>Data Fim</strong>
            </div>
            <div class="col">
              <strong>Vendedor</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input class="form form-control form-control-sm" type="date" name="DtInicial">
            </div>
            <div class="col">
              <input class="form form-control form-control-sm" type="date" name="DtFinal">
            </div>
            <div class="col">
              <select name="codVen" id="codVen" class="form-select form-select-sm">
                <option value="0">-- Selecione Vendedor --</option>
                <?php foreach ($consultaVendedor as $key => $item): ?>
                  <option value="<?= $item['usu_iderep'] ?>"><?= $item['aperep'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
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

<!-- Exibindo Resultado Analítico -->
<?php if (isset($Analitico)) : ?>
  <?php if ($codVen === '0') : ?>
    <div class="container d-flex justify-content-center">
      <div class="col col-sm-6">
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <h5 class="card-header bg-primary text-white">Período: <?= date('d/m/Y', strtotime($dtInicio)) ?> - <?= date('d/m/Y', strtotime($dtFim)) ?>
            </h5>
            <table class="table table-striped full-width-table mb-0">
              <thead>
                <tr class="table-primary">
                  <th>Vendedor</th>
                  <th>Valor Comissão</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($dadosAgrupados as $Vendedor => $dadosVendedor): ?>
                  <tr>
                    <td><?= $Vendedor ?></td>
                    <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format($dadosVendedor['SomaComissao'], 2, ',', '.') ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tbody>
                <tr class="table-primary">
                  <th>Total Comissão Geral</th>
                  <th style="text-align: right;"><span style="float: left;">R$</span><?= number_format($totalComissaoGeral, 2, ',', '.') ?></th>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
  <div class="mb-2"></div>
  <?php foreach ($dadosAgrupados as $Vendedor => $dadosVendedor): ?>
    <div class="container">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-header bg-primary text-white">
            Vendedor: <?= $Vendedor ?> <br>
            Qtde. Total: <?= $dadosVendedor['TotalVendasPorVendedor'] ?> ||
            Valor Total NF: <?= number_format($dadosVendedor['SomaNota'], 2, ',', '.') ?> ||
            Comissão Total R$ <?= number_format($dadosVendedor['SomaComissao'], 2, ',', '.') ?>
          </h5>
          <table id="Comissao" name="Comissao" class="table table-striped full-width-table mb-0">
            <thead>
              <tr>
                <th>Cliente</th>
                <th>Num. Ped.</th>
                <th>Num. Orc.</th>
                <th>Dt. Emissão</th>
                <th>Nota</th>
                <th>Tipo Nota</th>
                <th>Vlr. Nota</th>
                <th>% Agen</th>
                <th>Vlr. Com. Ag</th>
                <th>% Vend</th>
                <th>Vlr. Com. Ven</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($dadosVendedor['Itens'] as $item): ?>
                <?php if (is_array($item)): ?>
                  <tr>
                    <td><?= $item['Cliente'] ?></td>
                    <td style="text-align: center;"><?= $item['NumPedido'] ?></td>
                    <td ><?= $item['NumPedOrc'] ?></td>
                    <td><?= date('d/m/Y', strtotime($item['DtNF'])) ?></td>
                    <td><?= $item['NumNota'] ?></td>
                    <td><?= $item['TipoNota'] ?></td>
                    <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format($item['VlrNF'], 2, ',', '.') ?></td>
                    <td style="text-align: center;"><?= number_format($item['PerComisAgencia'], 2, ',', '.') ?></td>
                    <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format($item['VlrComisAg'], 2, ',', '.') ?></td>
                    <td style="text-align: center;"><?= number_format($item['PerComisVend'], 2, ',', '.') ?></td>
                    <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format($item['VlrComis'], 2, ',', '.') ?></td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            </tbody>
            <tbody>
              <tr class="table-primary">
                <th colspan="3" style="text-align: left; font-weight: bold;"></th>
                <th style="text-align: left; font-weight: bold;"> Qtde. Total </th>
                <th style="text-align: left; font-weight: bold;"><?= $dadosVendedor['TotalVendasPorVendedor'] ?></th>
                <th style="text-align: left; font-weight: bold;"> Valor Total NF: </th>
                <th style="text-align: right; font-weight: bold;"><span style="float: left;">R$</span><?= number_format($dadosVendedor['SomaNota'], 2, ',', '.'); ?></th>
                <th colspan="3" style="text-align: right; font-weight: bold;"> Valor Total Comissão </th>
                <th style="text-align: right; font-weight: bold;"><span style="float: left;">R$</span><?= number_format($dadosVendedor['SomaComissao'], 2, ',', '.') ?></th>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="mb-3"></div>
  <?php endforeach; ?>
<?php endif; ?>

<!-- Espaço entre o menu e o resultado -->
<div class="mb-3"></div>

<!-- Inclui o JavaScript da página -->
<script src="<?= URL_PRINCIPAL ?>js/graf_comissao.js"></script>
<!-- <script src="<?= URL_PRINCIPAL ?>js/exibirtabela.js"></script> -->

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>