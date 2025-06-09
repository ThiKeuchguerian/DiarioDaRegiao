<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/IntegracaoAnunDw.php';

$Titulo = 'Integração de Anúncios DW';
$URL = URL_PRINCIPAL . 'ti/IntegracaoAnunDw.php';

// Instanciar a classe
$IntegracaoAnunDw = new IntegracaoAnunDw();

if (isset($_POST['btn-buscar'])) {
  $MesAno = $_POST['MesAno'];
  $codAnuncio = $_POST['codAnuncio'];

  // echo "<pre>";
  // var_dump($MesAno, $codAnuncio);
  // die();

  $consultaAnunciosDw = $IntegracaoAnunDw->consultaAnunciosDw($MesAno, $codAnuncio);
  $consultaAnunciosProtheus = $IntegracaoAnunDw->consultaAnunciosProtheus($MesAno, $codAnuncio);
  $TotalAnuncios = COUNT($consultaAnunciosDw);

  // echo "<pre>";
  // var_dump($consultaAnunciosDw);
  // var_dump($consultaAnunciosProtheus);
  // die();

  $Capt     = 0;
  $EasyClass = 0;
  $Protheus = 0;
  foreach ($consultaAnunciosDw as $item)
    if ($item['Origem'] === '1') {
      $Capt++;
    } elseif ($item['Origem'] === '3') {
      $EasyClass++;
    }

  foreach ($consultaAnunciosProtheus as $item)
    $Protheus++;

  $protheusLookup = [];
  foreach ($consultaAnunciosProtheus as $p) {
    // normaliza DataVeiculacao no mesmo formato de DtVeic (YYYY-MM-DD)
    $key = preg_replace('/\D+$/', '', $p['Num']);
    $protheusLookup[$key] = true;
  }

  $easyClassLookup = [];
  foreach ($consultaAnunciosDw as $item) {
    if ($item['Origem'] === '1') {
      // normaliza DataVeiculacao no mesmo formato de DtVeic (YYYY-MM-DD)
      // Remove 'DR', 'D' ou qualquer letra no final e retorna apenas os números
      $key = preg_replace('/\D+$/', '', $item['NumeroAP']);
      $easyClassLookup[$key] = true;
    }
  }

  // Comparar linha por linha dos dois arrays
  $integrado = [];
  foreach ($easyClassLookup as $key => $val) {
    if (isset($protheusLookup[$key])) {
      $integrado[$key] = true;
    }
  }
  $integrado = count($integrado);
  $TotalAnuncios = $TotalAnuncios + $Protheus;
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
              <strong>Mês / Ano</strong>
            </div>
            <div class="col">
              <strong>Código Anúncio</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="MesAno" name="MesAno" placeholder="MM/YYYY">
            </div>
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="codAnuncio" name="codAnuncio">
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <div class="col text-end">
            <button id="btn-buscar" name="btn-buscar" type="submit" class="btn btn-primary btn-sm">Buscar</button>
            <button id="btn-exportar" name="btn-exportar" type="submit" class="btn btn-success btn-sm">Exportar</button>
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
<?php if (isset($TotalAnuncios)) : ?>
  <div class="container">
    <div class="card shadow-sm">
      <h5 class="card-header bg-primary text-white">
        Total Anúncios: <?= $TotalAnuncios ?> ||
        Qtde. Capt: <?= $Capt ?> ||
        Qtde. EasyClass: <?= $EasyClass ?> ||
        Qtde. Protheus: <?= $Protheus ?>
      </h5>
      <h5 class="card-header bg-primary text-white">
        Qtde. Anuncios Integrados Protheus: <?= $integrado ?>
      </h5>
      <div class="card-body">
        <div class="row" style="display: flex;">
          <div style="flex: 3; margin-right: 5px; overflow-x: auto;">
            <table class="table table-striped table-hover" style="min-width: 700px;">
              <thead>
                <tr class="table-primary">
                  <th>Numero</th>
                  <th>Cod. Cliente</th>
                  <th>Data Veic.</th>
                  <th>Data Emissão</th>
                  <th>Tipo</th>
                  <th>Vlr. Liquido</th>
                  <th>Vlr. Bruto</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($consultaAnunciosDw as $key => $item) : ?>
                  <?php if ($item['Origem'] === '1') : ?>
                    <tr>
                      <td><?= preg_replace('/\D+$/', '', $item['NumeroAP']) ?></td>
                      <td><?= $item['ChaveCliente'] ?></td>
                      <td><?= date('d/m/Y', strtotime($item['DataVeiculacao'])) ?></td>
                      <td><?= date('d/m/Y', strtotime($item['D2_EMISSAO'])) ?></td>
                      <td><?= $item['TipoVenda'] ?></td>
                      <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($item['ValorLiquido'], 2, ',', '.') ?></td>
                      <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($item['ValorBruto'], 2, ',', '.') ?></td>
                    </tr>
                  <?php endif; ?>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div style="flex: 2; overflow-x: auto;">
            <table class="table table-striped table-hover" style="min-width: 450px;">
              <thead>
                <tr class="table-primary" style="width: auto;">
                  <th>Numero</th>
                  <th>Cod. Cli.</th>
                  <th>Data Veic.</th>
                  <th>Data Emissão</th>
                  <th>Vlr. Liquido</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // Criar lookup dos NumeroAP da primeira tabela para comparação
                $numeroApLookup = [];
                foreach ($consultaAnunciosDw as $itemDw) {
                  $numeroApLookup[preg_replace('/\D+$/', '', $itemDw['NumeroAP'])] = true;
                }
                ?>
                <?php foreach ($consultaAnunciosProtheus as $key => $item) : ?>
                  <?php
                  $numLimpo = preg_replace('/\D+$/', '', $item['Num']);
                  $status = isset($numeroApLookup[$numLimpo]) ? '<span class="badge bg-success">OK</span>' : '';
                  ?>
                  <tr>
                    <td><?= $numLimpo ?></td>
                    <td><?= $item['Cli'] ?></td>
                    <td><?= date('d/m/Y', strtotime($item['DtVeic'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($item['DtEmi'])) ?></td>
                    <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($item['Vlr'], 2, ',', '.') ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Inclui JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
