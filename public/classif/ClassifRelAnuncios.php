<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/ClassifRelAnuncios.php';

$Titulo = 'Relatório Anuncios';
$URL = URL_PRINCIPAL . 'classif/ClassifRelAnuncios.php';

// Instanciar a classe
$ClassifRelAnuncios = new ClassifRelAnuncios();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn-buscar'])) {
  $DtCaptaca = $_POST['DtCaptacao'];
  $Bandeira = $_POST['BandeiraCartao'];
  $Integracao = $_POST['Integracao'];

  if ($Integracao === '') {
    $Integracao = null;
  }
  // echo "<pre>";
  // var_dump( $DtCaptaca, $Bandeira, $Integracao);
  // die();

  $RelAnuncios = $ClassifRelAnuncios->ConsultaAnuncios($DtCaptaca, $Bandeira, $Integracao);
  $QtdeTotal = COUNT($RelAnuncios);
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
              <strong>Data Captação</strong>
            </div>
            <div class="col">
              <strong>Bandeira</strong>
            </div>
            <div class="col">
              <strong>Integracao</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="date" class="form-control form-control-sm" id="DtCaptacao" name="DtCaptacao">
            </div>
            <div class="col">
              <select class="form-control form-control-sm" name="BandeiraCartao" id="BandeiraCartao">
                <option value="">--Selecione Bandeira --</option>
                <option value="Todas">Todas</option>
                <option value="Amex">Amex</option>
                <option value="Aura">Aura</option>
                <option value="Elo">Elo</option>
                <option value="Hiper">Hiper</option>
                <option value="Master">Master</option>
                <option value="Visa">Visa</option>
              </select>
            </div>
            <div class="col">
              <select class="form-control form-control-sm" name="Integracao" id="Integracao">
                <option value="">--Selecione Integração--</option>
                <option value="1"> SIM </option>
                <option value="2"> NAO </option>
              </select>
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

<!-- Resultado Consulta -->
<?php if (!empty($RelAnuncios)):
  $TotaAnuncios = 0;
  $SomaValorAnuncios = 0;
  foreach ($RelAnuncios as $key => $item) {
    $TotaAnuncios++;
    $SomaValorAnuncios += $item['ValorAnuncio'];
  }
?>
  <div class="container">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-header bg-primary text-white">
          Qtde. Anúncios:<?= $TotaAnuncios ?></span>
          ||
          Data dos Anúncios: <?= date("d/m/Y", strtotime($DtCaptaca)) ?>
          ||
          Valor Anúncios: R$ <?= number_format($SomaValorAnuncios, 2, ',', '.') ?>
        </h5>
        <table class="table table-striped table-hover">
          <thead>
            <tr class="table-primary">
              <th scope="col">Dt. Captação</th>
              <th scope="Col">Núm. Anuncio</th>
              <th scope="col">Nome Cliente</th>
              <th scope="col">Tipo Pag.</th>
              <th scope="col">Vlr Anuncio</th>
              <th scope="col">Bandeira</th>
              <th scope="col">Tipo Oper.</th>
              <th scope="col">Qtd. Parc.</th>
              <th scope="col">Num. Doc.</th>
              <th scope="col">Num. Autoriz.</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($RelAnuncios as $itens): //number_format((float)str_replace(',', '.', ($item['VlrPub'] ?? 0)), 2, ',', '.')?>
              <tr>
                <td><?= date('d/m/Y', strtotime($itens['DataCaptacao'])) ?></td>
                <td><?= intval($itens['NumAnuncio']) ?></td>
                <td><?= $itens['Cliente_Nome'] ?></td>
                <td><?= $itens['TipoPag'] ?></td>
                <td style="text-align: right; white-space: nowrap;"><span style="float: left; display: inline-block;">R$ </span><?= number_format($itens['ValorAnuncio'], 2, ',', '.') ?></td>
                <td><?= $itens['BandeiraCartao'] ?></td>
                <td><?= $itens['TipoOperCartao'] ?></td>
                <td><?= $itens['ParcelasCartao'] ?></td>
                <td><?= $itens['NumDoctoCartao'] ?></td>
                <td><?= $itens['NumAutorizCartao'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Inclui os Scripts -->
<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>
<script src="<?= URL_PRINCIPAL ?>js/exibirtabela.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>