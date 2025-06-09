<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Functions/ComConCadena.php';

$Titulo = 'Verifica Contrato Cadena (Numeros Comercial)';
$URL = URL_PRINCIPAL . 'comercial/ComConCadena.php';

// Instanciar a classe
$ComercialContratoCadena = new ComContratoCadena();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn-buscar'])) {
  $NrContrato = $_POST['NumContrato'];

  $DadosResultado = $ComercialContratoCadena->ConsultaContrato($NrContrato);
  $SomaResultado = $ComercialContratoCadena->SomaContrato($NrContrato);
  $Total = COUNT($DadosResultado);
}

// Inclui o header da página
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Menu de navegação -->
<div class="containers d-flex justify-content-center filter-fields">
  <div class="col col-sm-4">
    <div class="card shadow-sm">
      <form action=<?= $URL ?> method="post" id="CheckMetas" name="CheckMetas">
        <div class="card-header bg-primary text-white">
          <div class="row">
            <div class="col">
              <strong>N.º Contrato</strong>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col">
              <input type="text" class="form-control form-control-sm" id="NumContrato" name="NumContrato" maxlength="6" pattern="\d+" placeholder="001234">
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
<?php if (isset($DadosResultado)) : ?>
  <?php
  $vlrUnitarioSum = array_sum(array_map(function ($item) {
    return $item['VlrUnitario'];
  }, $DadosResultado));
  ?>
  <div class="container d-flex justify-content-center">
    <div class="card shadow-sm">
      <h5 class="card-header bg-primary text-white">
        Contrato N.º: <?= $NrContrato ?>    ||
        Qtde. Total Dias: <?= $Total ?> || 
        Valor Total: R$ <?= number_format(($vlrUnitarioSum ?? 0), 2, ',', '.') ?>
      </h5>
      <div class="card-body">
        <table class="table table-striped table-hover" id="Resultado" name="Resultado">
          <thead>
            <tr class="table-primary">
              <th>Mês Veiculação</th>
              <th>Valor Total</th>
              <th>Dias Veiculação</th>
              <th>Total Veiculação</th>
              <th>Vendedor Contrato</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($SomaResultado as $key => $item): ?>
              <tr>
                <td><?= $item['MesVeiculacao'] ?></td>
                <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format(($item['Valor'] ?? 0), 2, ',', '.') ?></td>
                <td style="text-align: center"><?= $item['DtVeiculacao'] ?></td>
                <td style="text-align: center"><?= $item['QtdeVeiculacao'] ?></td>
                <td><?= $item['NomeVend'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <thead>
            <tr class="table-primary">
              <th>Mês Veiculação</th>
              <th>Data Veiculação</th>
              <th>Grupo Programa</th>
              <th>Programa</th>
              <th>Vlr. Tabela</th>
              <th>Qtde.</th>
              <th>Vlr. Unitário</th>
              <th>Vlr. Total</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($DadosResultado as $key => $item): ?>
              <tr>
                <td style="text-align: center"><?= $item['MesVeiculacao'] ?></td>
                <td><?= $item['DtVeiculacao'] ?></td>
                <td><?= $item['GrupoPrograma'] ?></td>
                <td><?= $item['Programa'] ?></td>
                <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format(($item['VlrTabela'] ?? 0), 2, ',', '.') ?></td>
                <td style="text-align: center"><?= $item['Qtde'] ?></td>
                <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format(($item['VlrUnitario'] ?? 0), 2, ',', '.') ?></td>
                <td style="text-align: right;"><span style="float: left;">R$</span><?= number_format(($item['VlrTotal'] ?? 0), 2, ',', '.') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<script src="<?= URL_PRINCIPAL ?>js/maskcampos.js"></script>
<script src="<?= URL_PRINCIPAL ?>js/exibirtabela.js"></script>

<!-- Inclui o footer da página -->
<?php
require_once __DIR__ . '/../includes/footer.php';
?>