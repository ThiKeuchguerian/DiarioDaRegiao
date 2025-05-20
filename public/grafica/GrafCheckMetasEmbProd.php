<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/GrafCheckMetas.php';

$Titulo = 'CheckMetas Grafica - Embalagem / Produto';
$URL = URL_PRINCIPAL . 'grafica/GrafCheckMetasEmbProd.php';

// Instanciar a classe
$GraficaCheckMetasEmbProd = new GraficaCheckMetas();

if (isset($_POST['btn-buscar'])) {
  $ano = $_POST['Ano'];
  $tipo = $_POST['Tipo'];
  // echo "<pre>";
  // var_dump($ano, $tipo);
  // die();

  $SomaAnual = $GraficaCheckMetasEmbProd->consultaMetas($ano, $tipo);
  $Anual = COUNT($SomaAnual);

  // Todos os meses que apareceram
  $mesesApareceram = [];

  // Total Geral por mes
  $totalGeral = [];

  // Estrutura de Vendedores
  $vendedores = [];

  foreach ($SomaAnual as $item) {
    $vend     = $item['Vendedor'];
    $nomeCli  = $item['Cliente'];
    $descProd = $item['DescProduto'];
    $mes      = $item['MesAno'];      // ex: '01/2025'
    $qtde     = $item['QtdeVenda'];   // ou QtdeVenda, dependendo do que quer somar
    $vlr      = $item['VlrNF'];

    //  Guarda Mes
    $mesesApareceram[$mes] = $mes;

    // Total Geral
    $totalGeral[$mes] = ($totalGeral[$mes] ?? 0) + $vlr;

    // Vendedor
    if (!isset($vendedores[$vend])) {
      $vendedores[$vend] = [
        'meses'    => [],
        'clientes' => []
      ];
    }

    // Soma Qtde no Vendedor->Mes
    $vendedores[$vend]['meses'][$mes] = ($vendedores[$vend]['meses'][$mes] ?? 0) + $vlr;

    // Cliente no Vendedor
    if (!isset($vendedores[$vend]['clientes'][$nomeCli])) {
      $vendedores[$vend]['clientes'][$nomeCli] = [
        'Cliente'  => $nomeCli,
        'produtos' => []
      ];
    }

    // Soma Qtde no Produto
    $vendedores[$vend]['clientes'][$nomeCli]['produtos'][$descProd]['meses'][$mes] = ($vendedores[$vend]['clientes'][$nomeCli]['produtos'][$descProd]['meses'][$mes] ?? 0) + $qtde;
  }
  ksort($mesesApareceram);
  $meses = array_values($mesesApareceram);

  // helper para slug (usar no data-target)
  function slug($s)
  {
    return preg_replace('/[^a-z0-9]+/', '-', strtolower($s));
  }
}
// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center">
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
<?php if (isset($Anual)) : ?>
  <div class="container-fluid h-100">
    <div class="card shadow-sm h-100">
      <h5 class="card-header bg-primary text-white"></h5>
      <div class="card-body">
        <table id="Anual" class="table table-striped table-hover mb-0">
          <thead>
            <tr class="table-primary">
              <th>Vendedor / Cliente</th>
              <th>Produto</th>
              <?php foreach ($meses as $m): ?>
                <th style="text-align: center; white-space:nowrap;"><?= $m ?></th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <tr class="table-secondary">
              <th colspan="2">Total Geral</th>
              <?php foreach ($meses as $m) : ?>
                <?php $tg = $totalGeral[$m] ?? 0 ?>
                <td style="text-align: right; "><span style="float: left;">R$</span><?= number_format($tg, 2, ',', '.') ?></td>
              <?php endforeach; ?>
            </tr>
            <?php foreach ($vendedores as $vendedor => $dadosVend) : ?>
              <?php $slug = preg_replace('/[^a-z0-9]+/', '-', mb_strtolower($vendedor, 'UTF-8')) ?>
              <tr class="summary-row" data-vendedor="<?= $slug ?>" onclick="toggleDetails('<?= $slug ?>')">
                <th colspan="2">
                  <?= $vendedor ?>
                </th>
                <?php foreach ($meses as $m) : ?>
                  <?php $vv = $dadosVend['meses'][$m] ?? 0 ?>
                  <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format($vv, 2, ',', '.') ?></td>
                <?php endforeach; ?>
              </tr>
              <?php foreach ($dadosVend['clientes'] as $cli => $cliData): ?>
                <?php foreach ($cliData['produtos'] as $prd => $prdData): ?>
                  <tr class="detail-row hidden" data-vendedor="<?= $slug ?>">
                    <td><?= $cliData['Cliente'] ?></td>
                    <td><?= $prd ?></td>
                    <?php foreach ($meses as $m):
                      $pp = $prdData['meses'][$m] ?? 0;
                    ?>
                      <td style="text-align:right;"><?= number_format($pp, 0, ',', '.') ?></td>
                    <?php endforeach; ?>
                  </tr>
                <?php endforeach; ?>
              <?php endforeach; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="mb-3"></div>
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