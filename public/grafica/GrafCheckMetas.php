<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/GrafCheckMetas.php';

$Titulo = 'Check Metas Gráfica - Comercial / Embalagem';
$URL = URL_PRINCIPAL . 'grafica/GrafCheckMetas.php';

// Instanciar a classe
$GraficaCheckMetas = new GraficaCheckMetas();

if (isset($_POST['btn-buscar'])) {
  $ano = $_POST['Ano'];
  $tipo = $_POST['Tipo'];

  $SomaAnual = $GraficaCheckMetas->consultaMetas($ano, $tipo);
  $Anual = COUNT($SomaAnual);

  // 1. array onde vamos guardar:
  //    [TipoServico] ⇒
  //       [CodCli] ⇒ ['Cliente'=>nome, 'CodCli'=>código, 'meses'=>[MesAno=>soma]]
  $agrupado = [];
  // 2. totais gerais por TipoServico e por MesAno
  $totais   = [];
  // 3. uma lista de todos os MesAno que apareceram (para montar as colunas)
  $mesesApareceram = [];

  foreach ($SomaAnual as $item) {
    $tipo      = $item['TipoServico'];
    $codCli    = $item['CodCli'];
    $nomeCli   = $item['Cliente'];
    $mesAno    = $item['MesAno'];      // ex: '01/2025'
    $valorNota = $item['VlrNF'];       // ou QtdeVenda, dependendo do que quer somar

    //  guarda o mês para montar as colunas depois
    $mesesApareceram[$mesAno] = $mesAno;

    //  inicializa o grupo se não existir
    if (! isset($agrupado[$tipo][$codCli])) {
      $agrupado[$tipo][$codCli] = [
        'Cliente' => $nomeCli,
        'CodCli'  => $codCli,
        'meses'   => [],  // aqui vira [ '01/2025'=>1234.56, ... ]
      ];
    }

    //  acumula valor no cliente → mes
    $agrupado[$tipo][$codCli]['meses'][$mesAno] =
      ($agrupado[$tipo][$codCli]['meses'][$mesAno] ?? 0)
      + $valorNota;

    // acumula valor no total geral do tipo → mes
    $totais[$tipo][$mesAno] =
      ($totais[$tipo][$mesAno] ?? 0)
      + $valorNota;

    // acumula valor no total geral do tipo → mes
    $TotalGeral[$mesAno] =
      ($TotalGeral[$mesAno] ?? 0)
      + $valorNota;
  }

  // Ordenar os meses (se forem sempre 01/2025…12/2025 você pode gerar direto,
  //    mas aqui vamos ordenar o que apareceu)
  ksort($mesesApareceram);
  $meses = array_values($mesesApareceram);  // ex: ['01/2025','02/2025',…,'12/2025']

} elseif (isset($_POST['btn-analitico'])) {
  $ano = $_POST['Ano'];
  $tipo = $_POST['Tipo'];

  $consultaAnalitico = $GraficaCheckMetas->consultaMetas($ano, $tipo);
  $Analitico = COUNT($consultaAnalitico);
  // Monta o agrupamento em memória
  $agrupado = [];
  foreach ($consultaAnalitico as $item) {
    $ts   = $item['TipoServico'];
    $ma   = $item['MesAno'];
    // Insere a linha no grupo: TipoServico → MesAno → [lista de linhas]
    $agrupado[$ts][$ma][] = $item;
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
              <strong>Tipo</strong>
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
              <select class="form-select form-select-sm" id="Tipo" name="Tipo">
                <option value="0">-- Selecione --</option>
                <option value="1">Comercial</option>
                <option value="2">Embalagem</option>
              </select>
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

<!-- Exibindo Resultado Analítico -->
<?php if (isset($Analitico)) : ?>
  <?php foreach ($agrupado as $tipoServico => $meses) : ?>
    <div class="container">
      <div class="card shadow-sm h-100">
        <h5 class="card-header bg-primary text-white">Serviço: <?= $tipoServico ?></h5>
        <?php foreach ($meses as $mesAno => $linhas) : ?>
          <?php // opcional: total do Mês/Ano
          $somaQtde = array_sum(array_column($linhas, 'QtdeVenda'));
          $somaVlr  = array_sum(array_column($linhas, 'VlrNF'));
          ?>
          <h5 class="card-header bg-primary text-white">
            MesAno: <?= $mesAno ?> ||
            Qtde. Venda = <?= number_format($somaQtde, 0, ',', '.') ?> ||
            Valor Total = R$ <?= number_format($somaVlr, 2, ',', '.') ?>
          </h5>
          <div class="card-body">
            <table id="Analitico" class="table table-striped table-hover mb-0" style="border: 1px solid #ccc;">
              <thead>
                <tr class="table-primary">
                  <th>Cod. Cli.</th>
                  <th>Nome Cliente</th>
                  <th>Nº. Nota</th>
                  <th>Nº. Pedido</th>
                  <th>Tipo Nota</th>
                  <th>Tipo Serviço</th>
                  <th>Cod. Produto</th>
                  <th>Desc. Produto</th>
                  <th>Qtde.</th>
                  <th>Vlr. Nota</th>
                  <th>Vendedor</th>
                  <th>Mes/Ano</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($linhas as $item) : ?>
                  <tr>
                    <td><?= $item['CodCli'] ?></td>
                    <td><?= $item['Cliente'] ?></td>
                    <td style="white-space: nowrap;"><?= $item['Nota'] ?></td>
                    <td><?= $item['NumPedido'] ?></td>
                    <td style="white-space: nowrap;"><?= $item['TipoNota'] ?></td>
                    <td><?= $item['TipoServico'] ?></td>
                    <td><?= $item['CodProduto'] ?></td>
                    <td><?= $item['DescProduto'] ?></td>
                    <td style="text-align: right;"><?= number_format($item['QtdeVenda'], 0, ',', '.') ?></td>
                    <td style="text-align: right; white-space: nowrap;"><span style="float: left;">R$</span> <?= number_format($item['VlrNF'], 2, ',', '.') ?></td>
                    <td><?= $item['Vendedor'] ?></td>
                    <td><?= $item['MesAno'] ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="mb-3"></div>
  <?php endforeach; ?>
<?php endif; ?>

<!-- Exibindo Resultado Anual -->
<?php if (isset($Anual)) : ?>
  <div class="container">
    <div class="card shadow-sm  h-100">
      <h5 class="card-header bg-primary text-white">Total Geral: Embalagem/Comercial</h5>
      <div class="card-body">
        <table id="AnualGeral" class="table table-striped table-hover mb-0" style="border: 1px solid #ccc;">
          <thead>
            <tr class="table-primary">
              <?php foreach ($meses as $m) : ?>
                <th style="white-space:nowrap;"><?= $m ?></th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <tr class="table-primary">
              <?php foreach ($meses as $m) : ?>
                <?php $tv = $TotalGeral[$m] ?? 0 ?>
                <td style="text-align: right; white-space:nowrap;"><span style="float: left;">R$</span><?= number_format($tv, 2, ',', '.') ?></td>
              <?php endforeach; ?>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <?php foreach ($agrupado as $tipoServico => $clientes) : ?>
    <div class="container">
      <div class="card shadow-sm  h-100">
        <h5 class="card-header bg-primary text-white"></h5>
        <div class="card-body">
          <table id="AnualGeral" class="table table-striped table-hover mb-0" style="border: 1px solid #ccc;">
            <thead>
              <tr class="table-primary">
                <th>Serviço: <?= $tipoServico ?></th>
                <?php foreach ($meses as $m) : ?>
                  <th style="white-space:nowrap;"><?= $m ?></th>
                <?php endforeach; ?>
              </tr>
            </thead>
            <tbody>
              <tr class="table-primary">
                <th>Total Mês</th>
                <?php foreach ($meses as $m) : ?>
                  <?php $tv = $totais[$tipoServico][$m] ?? 0 ?>
                  <td style="text-align: right; white-space:nowrap;"><span style="float: left;">R$</span><?= number_format($tv, 2, ',', '.') ?></td>
                <?php endforeach; ?>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  <div class="mb-3"></div>
  <?php foreach ($agrupado as $tipoServico => $clientes) : ?>
    <div class="container">
      <div class="card shadow-sm  h-100">
        <h5 class="card-header bg-primary text-white">Serviço: <?= $tipoServico ?></h5>
        <div class="card-body">
          <table id="AnualGeral" class="table table-striped table-hover mb-0" style="border: 1px solid #ccc;">
            <thead>
              <tr class="table-primary">
                <th>Nome Cliente</th>
                <?php foreach ($meses as $m) : ?>
                  <th style="white-space:nowrap;"><?= $m ?></th>
                <?php endforeach; ?>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($clientes as $cli) : ?>
                <tr>
                  <td><?= $cli['Cliente'] ?></td>
                  <?php foreach ($meses as $m) : ?>
                    <?php $v = $cli['meses'][$m] ?? 0 ?>
                    <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format($v, 2, ',', '.') ?></td>
                  <?php endforeach; ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tbody>
              <tr class="table-secondary">
                <th>Total Mês</th>
                <?php foreach ($meses as $m) : ?>
                  <?php $tv = $totais[$tipoServico][$m] ?? 0 ?>
                  <td style="text-align: right; white-space:nowrap;"><span style="float: left;">R$</span><?= number_format($tv, 2, ',', '.') ?></td>
                <?php endforeach; ?>
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
<script src="<?= URL_PRINCIPAL ?>js/graf_checkmetas.js"></script>
<!-- <script src="<?= URL_PRINCIPAL ?>js/exibirtabela.js"></script> -->

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>