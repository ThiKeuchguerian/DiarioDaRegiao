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
  $TotalAnuncios = COUNT($consultaAnunciosDw);
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
              <input type="text" class="form-control form-control-sm" id="MesAno" name="MesAno" placeholder="MM/YYYY" required>
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
  <?php 
    $StatusOK = 0;
    $StatusXX = 0;
    $Capt     = 0;
    $EasyClass= 0;
    foreach ($consultaAnunciosDw as $item)
      if ($item['Origem'] === '1') {
        $Capt++;
      } elseif ($item['Origem'] === '3') {
        $EasyClass++;
      } 

      if ($item['ValorLiquido'] === $item['ValorBruto']) {
        $StatusOK++;
        $Status = '<span class="badge bg-primary">OK</span>';
      } else {
        $StatusXX++;
        $Status = '<span class="badge bg-danger">XX</span>';
      }
  ?>
  <div class="container">
    <div class="card shadow-sm">
      <h5 class="card-header bg-primary text-white">
        Total Anúncios: <?= $TotalAnuncios ?> ||
        Qtde. Capt: <?= $Capt ?> ||
        Qtde. EasyClass: <?= $EasyClass ?> 
        Qtde. Anuncios por Status: OK = <?= $StatusOK ?> XX = <?= $StatusXX ?>
      </h5>
      <div class="card-body">
        <table class="table table-striped table-hover">
          <thead>
            <tr class="table-primary">
              <th>Numero</th>
              <th>Cod. Cliente</th>
              <th>Data Veic.</th>
              <th>Data Emissão</th>
              <th>Tipo</th>
              <th>Vlr. Liquido</th>
              <th>Vlr. Bruto</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($consultaAnunciosDw as $key => $item) : ?>
              <tr>
                <td><?= $item['NumeroAP'] ?></td>
                <td><?= $item['ChaveCliente'] ?></td>
                <td><?= date('d/m/Y', strtotime($item['DataVeiculacao'])) ?></td>
                <td><?= date('d/m/Y', strtotime($item['D2_EMISSAO'])) ?></td>
                <td><?= $item['TipoVenda'] ?></td>
                <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($item['ValorLiquido'], 2, ',', '.') ?></td>
                <td style="text-align: right;"><span style="float: left;">R$ </span><?= number_format($item['ValorBruto'], 2, ',', '.') ?></td>
                <td style="text-align: center;"></span><?= $Status ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Inclui JavaScript -->
<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';